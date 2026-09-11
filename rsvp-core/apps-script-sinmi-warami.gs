/**
 * PackageWithSense RSVP sync — White Wedding (sinmi-warami) only.
 * Reference file, not deployed by Claude Code. Paste into a bound or
 * standalone Apps Script project attached to the destination Google Sheet.
 *
 * Setup:
 *   1. In the Sheet, create a tab named exactly "sinmi-warami" (must match
 *      SHEET_NAME below).
 *   2. Extensions -> Apps Script, paste this whole file in, save.
 *   3. Set SYNC_API_KEY below to match rsvp-core/.env's SYNC_API_KEY.
 *   4. Run the `syncRsvps` function once from the editor (select it in the
 *      function dropdown, click Run). It self-installs a 1-minute
 *      time-driven trigger the first time it runs — you don't need to touch
 *      the Triggers UI at all after that. Authorize the script when
 *      prompted (Google will warn it's unverified — expected for a private
 *      script; Advanced -> Go to project (unsafe) -> Allow).
 *
 * Two functions, two purposes:
 *   - syncRsvps()    Normal, automatic, incremental. Runs every minute via
 *                     its self-installed trigger. Only pulls submissions
 *                     newer than the last one it saw.
 *   - syncAllRsvps() Manual, full rebuild — but scoped to TODAY ONLY (see
 *                     REBUILD_TODAY_ONLY below). Ignores last_seen_id,
 *                     WIPES the sheet's data below the header row, and
 *                     re-fetches + re-writes every white-only submission
 *                     created since local midnight today. Use this if
 *                     you've deleted rows in the sheet by mistake today (or
 *                     want to rebuild after formatting changes) — run it
 *                     manually from the Apps Script editor (select
 *                     syncAllRsvps in the function dropdown, click Run). It
 *                     also resets last_seen_id, so the next automatic run
 *                     continues forward normally from there.
 *
 * Behavior:
 *   - Writes EVERY form's submissions into this one sheet (FORM_SLUG_FILTER
 *     is null). Both the trad-white and white-only RSVP forms land here, and
 *     the "Form Slug" column tells them apart. Columns are the union of both
 *     forms' fields, so a white-only row simply leaves the trad_* columns
 *     blank and vice versa. Set FORM_SLUG_FILTER to a specific slug to
 *     narrow this sheet back to a single form.
 *   - Columns are LIMITED to FIXED_COLUMNS plus RELEVANT_PAYLOAD_KEYS below
 *     — i.e. only the actual White Wedding form fields. Any other payload
 *     key (from a different/older form version, junk, etc.) is ignored
 *     rather than auto-appended as a new column, so the sheet stays clean.
 *   - Cell VALUES are cleaned before writing:
 *       - "phone" has all spaces and "+" characters stripped, regardless of
 *         how the guest typed it in.
 *       - Array values (e.g. white_asoebi_item, a multi-select checkbox
 *         field) are joined into a single comma-separated string. Without
 *         this, Apps Script writes the raw JS array's underlying Java
 *         object into the cell, which shows up as garbage like
 *         "[Ljava.lang.Object;@382529b" instead of the actual selections.
 *       - Any string value with underscores (e.g. an option value like
 *         "cotton_senator_4yd") has every underscore replaced with a space
 *         ("cotton senator 4yd") so raw HTML option values never leak into
 *         the sheet looking like code. This is applied per-element for
 *         array values too.
 *   - Header row cells are display-formatted (underscores stripped, each
 *     word capitalized — e.g. "full_name" -> "Full Name").
 *   - `id` is intentionally never written to the sheet. `form_slug` IS
 *     written, since this tab holds both forms.
 *   - syncRsvps() only advances last_seen_id after its batch is fully
 *     appended, so a failed run safely retries next time.
 *   - Guestbook notes ("Leave a SWeetLove note...") are a separate table/
 *     endpoint (get-notes.php) — not included in this script.
 */

const SHEET_NAME = 'sinmi-warami';
// null = capture EVERY form's submissions into this one sheet (trad-white and
// white-only alike). Set it to a specific slug ('white-only' / 'trad-white')
// if you ever want this sheet narrowed to just one form again.
const FORM_SLUG_FILTER = null;
// Currently deployed under /forms/ — drop that segment if/when the folders
// move to the WordPress root.
const ENDPOINT_URL = 'https://packagewithsense.com/forms/rsvp-core/get-new.php';
// Paste the real key here in the Apps Script editor only — never commit it.
// Must match SYNC_API_KEY in rsvp-core/.env on the server.
const SYNC_API_KEY = 'PASTE_SYNC_API_KEY_IN_APPS_SCRIPT_EDITOR';

// syncAllRsvps() only rebuilds rows created_at >= local midnight today when
// this is true. Set to false to go back to a full-history rebuild.
const REBUILD_TODAY_ONLY = true;

// 'id' is deliberately excluded. 'form_slug' IS included now that this sheet
// holds both forms — it's the only way to tell a trad-white row from a
// white-only one at a glance.
const FIXED_COLUMNS = [
    'form_slug',
    'full_name', 'email', 'phone',
    'rsvp_category', 'guest_count', 'created_at',
];

// Only these payload keys become columns; anything else in the payload is
// ignored (no auto-appended columns for stray/unexpected keys). This is the
// UNION of both forms' fields — a white-only submission simply leaves the
// trad_* columns blank, and vice versa.
const RELEVANT_PAYLOAD_KEYS = [
    // Traditional wedding (trad-white form only)
    'attending_trad',
    'trad_asoebi_purchase',
    'trad_asoebi_item',
    'trad_asoebi_qty',
    'trad_asoebi_delivery',
    'trad_asoebi_address',
    // White wedding (both forms)
    'attending_white',
    'white_asoebi_purchase',
    'white_asoebi_item',
    'white_asoebi_qty',
    'white_asoebi_delivery',
    'white_asoebi_address',
    'payment_method',
    // submit.php saves each uploaded file and records its public URL under
    // "<field_name>_url" — so these are the *_url keys, NOT the bare field
    // names. Using the bare names here yields blank columns.
    'trad_payment_evidence_url',
    'white_payment_evidence_url',
];

// ---------- Normal incremental sync (runs every minute) ----------

function syncRsvps() {
    ensureTrigger_();

    const props = PropertiesService.getScriptProperties();
    const lastSeenId = parseInt(props.getProperty('last_seen_id') || '0', 10);

    try {
        const allRows = fetchRows_(lastSeenId);
        if (!allRows.length) {
            return;
        }

        // Advance past every row we saw, even ones we don't write, so the
        // next run doesn't keep re-fetching the other form's submissions.
        let maxId = lastSeenId;
        allRows.forEach(function (row) {
            if (row.id > maxId) maxId = row.id;
        });

        const rows = allRows.filter(matchesFilter_);

        if (rows.length) {
            const sheet = getSheet_();
            writeRows_(sheet, rows);
        }

        props.setProperty('last_seen_id', String(maxId));
    } catch (err) {
        // Don't advance last_seen_id — next run retries the same batch.
        console.error('sinmi-warami RSVP sync failed: ' + err);
    }
}

// ---------- Manual full rebuild (run by hand when needed) ----------

// Ignores last_seen_id, wipes everything below the header row, and
// re-fetches + re-writes white-only submissions. Scoped to today only when
// REBUILD_TODAY_ONLY is true (the default) — see file header.
function syncAllRsvps() {
    try {
        const allRows = fetchRows_(0);

        const sheet = getSheet_();

        // Wipe existing data (and any header from a previous run) so the
        // rebuild starts completely clean — otherwise old columns/rows
        // could linger next to the freshly rebuilt ones.
        sheet.clearContents();

        const todayStart = startOfTodayLocal_();

        const rows = allRows.filter(function (row) {
            if (!matchesFilter_(row)) return false;
            if (!REBUILD_TODAY_ONLY) return true;
            return new Date(row.created_at) >= todayStart;
        });

        if (rows.length) {
            writeRows_(sheet, rows);
        }

        // Reset the bookmark to the newest id seen (across BOTH forms, same
        // as syncRsvps does) so the next automatic run picks up cleanly
        // from here instead of re-fetching this same full history again.
        let maxId = 0;
        allRows.forEach(function (row) {
            if (row.id > maxId) maxId = row.id;
        });
        PropertiesService.getScriptProperties().setProperty('last_seen_id', String(maxId));

        console.log('syncAllRsvps: rebuilt ' + rows.length + ' white-only row(s)' +
            (REBUILD_TODAY_ONLY ? ' (today only).' : ' (full history).'));
    } catch (err) {
        console.error('sinmi-warami RSVP full resync failed: ' + err);
    }
}

// FORM_SLUG_FILTER === null means "take every form's submissions".
function matchesFilter_(row) {
    return FORM_SLUG_FILTER === null || row.form_slug === FORM_SLUG_FILTER;
}

function startOfTodayLocal_() {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth(), now.getDate());
}

// ---------- Shared helpers ----------

function fetchRows_(sinceId) {
    const url = ENDPOINT_URL + '?since_id=' + sinceId + '&key=' + encodeURIComponent(SYNC_API_KEY);
    const response = UrlFetchApp.fetch(url, { muteHttpExceptions: true });

    if (response.getResponseCode() !== 200) {
        throw new Error('Sync endpoint returned HTTP ' + response.getResponseCode() + ': ' + response.getContentText());
    }

    return JSON.parse(response.getContentText());
}

function getSheet_() {
    const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(SHEET_NAME);
    if (!sheet) {
        throw new Error('Sheet "' + SHEET_NAME + '" not found');
    }
    return sheet;
}

// Writes multiple rows. Header row is fixed to FIXED_COLUMNS +
// RELEVANT_PAYLOAD_KEYS — no auto-appending of unexpected payload keys.
function writeRows_(sheet, rows) {
    let headers = getHeaders(sheet);
    if (headers.length === 0) {
        headers = FIXED_COLUMNS.concat(RELEVANT_PAYLOAD_KEYS);
        sheet.getRange(1, 1, 1, headers.length).setValues([headers.map(formatHeader)]);
    }

    rows.forEach(function (row) {
        const rowValues = headers.map(function (header) {
            let value;
            if (header === 'created_at') {
                value = row.created_at;
            } else if (FIXED_COLUMNS.indexOf(header) !== -1) {
                value = row[header] != null ? row[header] : '';
            } else {
                const payloadValue = row.payload ? row.payload[header] : undefined;
                value = payloadValue != null ? payloadValue : '';
            }
            return cleanValue_(header, value);
        });

        sheet.appendRow(rowValues);
    });
}

// Installs the 1-minute time-driven trigger for syncRsvps if it isn't
// already installed. Safe to call every run — it's a no-op once the
// trigger exists. This is what makes "just run it once" self-sufficient.
function ensureTrigger_() {
    const alreadyInstalled = ScriptApp.getProjectTriggers().some(function (t) {
        return t.getHandlerFunction() === 'syncRsvps' && t.getEventType() === ScriptApp.EventType.CLOCK;
    });
    if (!alreadyInstalled) {
        ScriptApp.newTrigger('syncRsvps').timeBased().everyMinutes(1).create();
    }
}

// Cleans a single cell value before it's written:
//  - Arrays (e.g. white_asoebi_item, a multi-select checkbox field) are
//    joined into a comma-separated string. Without this, Apps Script
//    writes the raw array's underlying Java object into the cell, which
//    renders as garbage like "[Ljava.lang.Object;@382529b" instead of the
//    actual selections.
//  - "phone": strips every space and "+".
//  - any string (including each element of an array, before joining):
//    replaces every underscore with a space, so raw option values (e.g.
//    "cotton_senator_4yd") read as "cotton senator 4yd".
function cleanValue_(header, value) {
    // Never rewrite a URL — underscore-stripping would corrupt the path and
    // produce a dead link (e.g. ".../packagewithsense_forms/..." would become
    // ".../packagewithsense forms/...").
    if (isUrlValue_(header, value)) {
        return value;
    }
    if (Array.isArray(value)) {
        return value
            .map(function (v) { return String(v).split('_').join(' '); })
            .join(', ');
    }
    if (typeof value !== 'string') return value;
    let cleaned = value;
    if (header === 'phone') {
        cleaned = cleaned.replace(/[\s+]/g, '');
    }
    cleaned = cleaned.split('_').join(' ');
    return cleaned;
}

function isUrlValue_(header, value) {
    if (typeof value !== 'string') return false;
    return header.slice(-4) === '_url' || value.indexOf('http://') === 0 || value.indexOf('https://') === 0;
}

// Display formatting for header cells: "full_name" -> "Full Name".
function formatHeader(key) {
    return key.split('_').map(function (word) {
        if (!word) return word;
        return word.charAt(0).toUpperCase() + word.slice(1);
    }).join(' ');
}

// Inverse of formatHeader, so headers already written to the sheet
// ("Full Name") map back to the raw key ("full_name") used everywhere
// else in this script for matching against FIXED_COLUMNS/RELEVANT_PAYLOAD_KEYS.
function rawKeyFromHeader(header) {
    return header.split(' ').join('_').toLowerCase();
}

function getHeaders(sheet) {
    const lastCol = sheet.getLastColumn();
    if (lastCol === 0) return [];
    return sheet.getRange(1, 1, 1, lastCol).getValues()[0]
        .filter(function (h) { return h !== ''; })
        .map(rawKeyFromHeader);
}