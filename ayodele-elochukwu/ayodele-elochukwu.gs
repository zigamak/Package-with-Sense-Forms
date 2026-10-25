/**
 * PackageWithSense RSVP sync — Ayodele & Elochukwu (form_slug 'ayodele-elochukwu').
 * Reference file, not deployed. Paste into an Apps Script project bound to
 * the destination Google Sheet.
 *
 * Setup:
 *   1. Open the destination Google Sheet. The "ayodele-elochukwu" tab is
 *      created automatically on the first sync if it doesn't exist.
 *   2. Extensions -> Apps Script, paste this whole file in, save.
 *   3. In the Apps Script editor, replace PASTE_SYNC_API_KEY_HERE below with
 *      rsvp-core/.env's SYNC_API_KEY. Never commit the real key: the repo
 *      is public and the key exposes every guest's contact details.
 *   4. Run `syncAllRsvps` once from the editor to pull every existing
 *      Ayodele & Elochukwu RSVP. Authorize when prompted (Google warns it's
 *      unverified — expected; Advanced -> Go to project (unsafe) -> Allow).
 *   5. Run `syncRsvps` once. It self-installs a 1-minute time-driven
 *      trigger, after which new RSVPs appear automatically.
 *
 * Two functions:
 *   - syncRsvps()    Automatic, incremental. Only pulls submissions newer
 *                     than the last one it saw.
 *   - syncAllRsvps() Manual full rebuild. Wipes the tab, re-writes every
 *                     Ayodele & Elochukwu RSVP ever submitted, and resets
 *                     last_seen_id. Use after deleting rows by mistake or
 *                     changing COLUMNS.
 *
 * Form fields (ayodele-elochukwu/index.php) -> where they arrive from
 * get-new.php:
 *   full_name, email, phone, guest_count   top-level columns
 *   title, guest_names, message             payload JSON
 *
 * The endpoint returns every form's rows (max 500 per request), so this
 * script pages through them and keeps only form_slug 'ayodele-elochukwu'.
 */

const SHEET_NAME = 'ayodele-elochukwu';
const FORM_SLUG = 'ayodele-elochukwu';
// Currently deployed under /forms/ — drop that segment if/when the folders
// move to the WordPress root.
const ENDPOINT_URL = 'https://packagewithsense.com/forms/rsvp-core/get-new.php';
const SYNC_API_KEY = 'PASTE_SYNC_API_KEY_HERE';
// get-new.php's LIMIT — a full page means there may be more rows to fetch.
const PAGE_SIZE = 500;

// Sheet columns, in order. `source` says where the value lives on each row
// returned by get-new.php: 'row' = top-level column, 'payload' = payload JSON.
const COLUMNS = [
    { key: 'created_at',  label: 'Submitted At',    source: 'row' },
    { key: 'title',       label: 'Title',           source: 'payload' },
    { key: 'full_name',   label: 'Full Name',       source: 'row' },
    { key: 'phone',       label: 'Phone Number',    source: 'row' },
    { key: 'email',       label: 'Email Address',   source: 'row' },
    { key: 'guest_count', label: 'Number Attending', source: 'row' },
    { key: 'guest_names', label: 'Guest Name(s)',   source: 'payload' },
    { key: 'message',     label: 'Message',         source: 'payload' },
];

// Raw option values from the form -> the labels guests actually saw.
const VALUE_LABELS = {
    guest_count: { just_me: 'Just me', plus_one: 'Me plus one', family: 'Family' },
};

// ---------- Normal incremental sync (runs every minute) ----------

function syncRsvps() {
    ensureTrigger_();

    const props = PropertiesService.getScriptProperties();
    const lastSeenId = parseInt(props.getProperty('last_seen_id') || '0', 10);

    try {
        const result = fetchAllSince_(lastSeenId);
        if (!result.rows.length) {
            return;
        }

        const rows = result.rows.filter(matchesForm_);
        if (rows.length) {
            writeRows_(getSheet_(), rows);
        }

        // Advance past every row seen (other forms included) so the next run
        // doesn't keep re-fetching them. Only reached after a successful write.
        props.setProperty('last_seen_id', String(result.maxId));
    } catch (err) {
        // Don't advance last_seen_id — next run retries the same batch.
        console.error('ayodele-elochukwu RSVP sync failed: ' + err);
    }
}

// ---------- Manual full rebuild (run by hand when needed) ----------

function syncAllRsvps() {
    try {
        const result = fetchAllSince_(0);
        const rows = result.rows.filter(matchesForm_);

        const sheet = getSheet_();
        sheet.clearContents();
        writeRows_(sheet, rows);

        PropertiesService.getScriptProperties().setProperty('last_seen_id', String(result.maxId));
        console.log('syncAllRsvps: rebuilt ' + rows.length + ' ayodele-elochukwu row(s).');
    } catch (err) {
        console.error('ayodele-elochukwu RSVP full resync failed: ' + err);
    }
}

// ---------- Helpers ----------

function matchesForm_(row) {
    return row.form_slug === FORM_SLUG;
}

// Pages through get-new.php until a short page comes back.
function fetchAllSince_(sinceId) {
    let all = [];
    let cursor = sinceId;
    while (true) {
        const page = fetchRows_(cursor);
        all = all.concat(page);
        page.forEach(function (row) {
            const id = parseInt(row.id, 10);
            if (id > cursor) cursor = id;
        });
        if (page.length < PAGE_SIZE) break;
    }
    return { rows: all, maxId: cursor };
}

function fetchRows_(sinceId) {
    const url = ENDPOINT_URL + '?since_id=' + sinceId + '&key=' + encodeURIComponent(SYNC_API_KEY);
    const response = UrlFetchApp.fetch(url, { muteHttpExceptions: true });

    if (response.getResponseCode() !== 200) {
        throw new Error('Sync endpoint returned HTTP ' + response.getResponseCode() + ': ' + response.getContentText());
    }

    return JSON.parse(response.getContentText());
}

// Creates the tab on first use if it doesn't exist yet.
function getSheet_() {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    return ss.getSheetByName(SHEET_NAME) || ss.insertSheet(SHEET_NAME);
}

// Writes the header row if the tab is empty, then appends all rows in one
// batch. Phone is formatted as plain text so leading zeros survive.
function writeRows_(sheet, rows) {
    if (sheet.getLastRow() === 0) {
        const labels = COLUMNS.map(function (c) { return c.label; });
        sheet.getRange(1, 1, 1, labels.length).setValues([labels]).setFontWeight('bold');
        sheet.setFrozenRows(1);
        const phoneCol = columnIndex_('phone') + 1;
        sheet.getRange(1, phoneCol, sheet.getMaxRows(), 1).setNumberFormat('@');
    }

    if (!rows.length) return;

    const values = rows.map(function (row) {
        return COLUMNS.map(function (col) {
            const raw = col.source === 'row'
                ? row[col.key]
                : (row.payload ? row.payload[col.key] : undefined);
            return cleanValue_(col.key, raw);
        });
    });

    const startRow = sheet.getLastRow() + 1;
    sheet.getRange(startRow, 1, values.length, COLUMNS.length).setValues(values);
}

function columnIndex_(key) {
    for (let i = 0; i < COLUMNS.length; i++) {
        if (COLUMNS[i].key === key) return i;
    }
    return -1;
}

// Cleans a single cell value before it's written:
//  - null/undefined -> blank
//  - arrays joined into a comma-separated string (avoids
//    "[Ljava.lang.Object;@..." garbage in the cell)
//  - known option values mapped to their on-page labels (VALUE_LABELS)
//  - phone: spaces and "+" stripped
//  - free text starting with = + - @ is prefixed with ' so Sheets never
//    evaluates a guest's input as a formula
function cleanValue_(key, value) {
    if (value == null) return '';
    if (Array.isArray(value)) {
        value = value.join(', ');
    }
    value = String(value).trim();

    const labels = VALUE_LABELS[key];
    if (labels && Object.prototype.hasOwnProperty.call(labels, value)) {
        return labels[value];
    }

    if (key === 'phone') {
        return value.replace(/[\s+]/g, '');
    }

    if (/^[=+\-@]/.test(value)) {
        return "'" + value;
    }
    return value;
}

// Installs the 1-minute time-driven trigger for syncRsvps if it isn't
// already installed. No-op once the trigger exists.
function ensureTrigger_() {
    const alreadyInstalled = ScriptApp.getProjectTriggers().some(function (t) {
        return t.getHandlerFunction() === 'syncRsvps' && t.getEventType() === ScriptApp.EventType.CLOCK;
    });
    if (!alreadyInstalled) {
        ScriptApp.newTrigger('syncRsvps').timeBased().everyMinutes(1).create();
    }
}
