/**
 * PackageWithSense RSVP sync — Traditional + White Wedding (sinmi-warami-trad-white) only.
 * Reference file, not deployed by Claude Code.
 *
 * This is the trad-white counterpart to apps-script-sinmi-warami.gs. Same
 * logic throughout — only SHEET_NAME, FORM_SLUG_FILTER, FIXED_COLUMNS and
 * RELEVANT_PAYLOAD_KEYS differ.
 *
 * IMPORTANT: paste this into its OWN, SEPARATE Apps Script project (not
 * alongside the white-only script). Each project keeps its own
 * PropertiesService store, which is what lets the two scripts track their
 * own last_seen_id independently without fighting each other. Two copies in
 * one project would also collide on duplicate function names.
 *
 * Setup:
 *   1. In the Sheet, create a tab named exactly "sinmi-warami-trad-white"
 *      (must match SHEET_NAME below).
 *   2. Extensions -> Apps Script -> (+) new project, paste this whole file in, save.
 *   3. Set SYNC_API_KEY below to match rsvp-core/.env's SYNC_API_KEY.
 *   4. Run `syncRsvps` once from the editor (function dropdown -> Run). It
 *      self-installs a 1-minute time-driven trigger on first run. Authorize
 *      when prompted (Advanced -> Go to project (unsafe) -> Allow).
 *
 * Two functions, two purposes:
 *   - syncRsvps()    Normal, automatic, incremental. Every minute.
 *   - syncAllRsvps() Manual full rebuild, scoped to TODAY ONLY while
 *                     REBUILD_TODAY_ONLY is true. Wipes the sheet and
 *                     re-writes today's trad-white submissions.
 *
 * Notes specific to this form:
 *   - trad_asoebi_item and white_asoebi_item are CHECKBOX groups (name="...[]"),
 *     so they arrive as arrays and get joined into one comma-separated cell
 *     by cleanValue_().
 *   - There are TWO uploads on this form (trad and white asoebi payments), so
 *     there are two evidence URL columns.
 *   - guest_count is intentionally NOT a column here — that question isn't on
 *     this form. Add 'guest_count' to FIXED_COLUMNS if it's ever re-added.
 */

const SHEET_NAME = 'sinmi-warami-trad-white';
const FORM_SLUG_FILTER = 'trad-white';
// Currently deployed under /forms/ — drop that segment if/when the folders
// move to the WordPress root.
const ENDPOINT_URL = 'https://packagewithsense.com/forms/rsvp-core/get-new.php';
// Paste the real key here in the Apps Script editor only — never commit it.
// Must match SYNC_API_KEY in rsvp-core/.env on the server.
const SYNC_API_KEY = 'PASTE_SYNC_API_KEY_IN_APPS_SCRIPT_EDITOR';

// syncAllRsvps() only rebuilds rows created_at >= local midnight today when
// this is true. Set to false to go back to a full-history rebuild.
const REBUILD_TODAY_ONLY = true;

// 'id' and 'form_slug' deliberately excluded — never saved to the sheet.
const FIXED_COLUMNS = [
    'full_name', 'email', 'phone',
    'rsvp_category', 'created_at',
];

// Only these payload keys become columns — matches the actual Traditional +
// White RSVP form fields in sinmi-warami-trad-white/index.php.
const RELEVANT_PAYLOAD_KEYS = [
    'attending_trad',
    'trad_asoebi_purchase',
    'trad_asoebi_item',
    'trad_asoebi_qty',
    'trad_asoebi_delivery',
    'trad_asoebi_address',
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

        const rows = allRows.filter(function (row) {
            return row.form_slug === FORM_SLUG_FILTER;
        });

        if (rows.length) {
            const sheet = getSheet_();
            writeRows_(sheet, rows);
        }

        props.setProperty('last_seen_id', String(maxId));
    } catch (err) {
        // Don't advance last_seen_id — next run retries the same batch.
        console.error('sinmi-warami-trad-white RSVP sync failed: ' + err);
    }
}

// ---------- Manual full rebuild (run by hand when needed) ----------

function syncAllRsvps() {
    try {
        const allRows = fetchRows_(0);

        const sheet = getSheet_();
        sheet.clearContents();

        const todayStart = startOfTodayLocal_();

        const rows = allRows.filter(function (row) {
            if (row.form_slug !== FORM_SLUG_FILTER) return false;
            if (!REBUILD_TODAY_ONLY) return true;
            return new Date(row.created_at) >= todayStart;
        });

        if (rows.length) {
            writeRows_(sheet, rows);
        }

        let maxId = 0;
        allRows.forEach(function (row) {
            if (row.id > maxId) maxId = row.id;
        });
        PropertiesService.getScriptProperties().setProperty('last_seen_id', String(maxId));

        console.log('syncAllRsvps: rebuilt ' + rows.length + ' trad-white row(s)' +
            (REBUILD_TODAY_ONLY ? ' (today only).' : ' (full history).'));
    } catch (err) {
        console.error('sinmi-warami-trad-white RSVP full resync failed: ' + err);
    }
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
// already installed. Safe to call every run — no-op once the trigger exists.
function ensureTrigger_() {
    const alreadyInstalled = ScriptApp.getProjectTriggers().some(function (t) {
        return t.getHandlerFunction() === 'syncRsvps' && t.getEventType() === ScriptApp.EventType.CLOCK;
    });
    if (!alreadyInstalled) {
        ScriptApp.newTrigger('syncRsvps').timeBased().everyMinutes(1).create();
    }
}

// Cleans a single cell value before it's written:
//  - URLs are left completely alone (see below).
//  - Arrays (the asoebi item checkbox groups) are joined into a
//    comma-separated string. Without this, Apps Script writes the raw
//    array's underlying Java object into the cell, rendering as garbage
//    like "[Ljava.lang.Object;@382529b".
//  - "phone": strips every space and "+".
//  - any other string: replaces underscores with spaces, so raw option
//    values ("cotton_senator") read as "cotton senator".
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
// ("Full Name") map back to the raw key ("full_name") used everywhere else.
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
