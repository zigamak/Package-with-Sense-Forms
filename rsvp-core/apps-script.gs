/**
 * PackageWithSense RSVP sync — Google Apps Script (reference file, not deployed
 * by Claude Code). Paste into a bound or standalone Apps Script project attached
 * to the destination Google Sheet.
 *
 * Setup:
 *   1. Set SHEET_NAME below to the target sheet/tab name.
 *   2. Set ENDPOINT_URL and SYNC_API_KEY to match rsvp-core/config.php's SYNC_API_KEY.
 *   3. In the Apps Script editor: Triggers -> Add Trigger -> syncRsvps ->
 *      Time-driven -> Minutes timer -> Every 5 minutes.
 *
 * Behavior:
 *   - Reads last_seen_id from PropertiesService (defaults to 0).
 *   - Fetches new rows from get-new.php.
 *   - Flattens each row's payload into columns, matching against the sheet's
 *     header row; unknown payload keys get a new column appended.
 *   - Writes form_slug, full_name, email, phone, rsvp_category, guest_count,
 *     created_at, then payload fields (which include attending_trad /
 *     attending_white, since attendance is event-specific, not a fixed column).
 *   - Guestbook notes ("Leave a SWeetLove note...") are NOT part of this sync —
 *     they live in the separate guest_notes table. Pull those into a Sheet
 *     with a second, near-identical script pointed at get-notes.php instead.
 *   - Only advances last_seen_id after all rows are successfully appended.
 */

const SHEET_NAME = 'RSVPs';
// Currently deployed under /forms/ — drop that segment if/when the folders
// move to the WordPress root (see README's "Why real folders" section).
const ENDPOINT_URL = 'https://packagewithsense.com/forms/rsvp-core/get-new.php';
const SYNC_API_KEY = 'CHANGE_ME_32_CHAR_RANDOM_STRING';

const FIXED_COLUMNS = [
    'id', 'form_slug', 'full_name', 'email', 'phone',
    'rsvp_category', 'guest_count', 'created_at',
];

function syncRsvps() {
    const props = PropertiesService.getScriptProperties();
    const lastSeenId = parseInt(props.getProperty('last_seen_id') || '0', 10);

    try {
        const url = ENDPOINT_URL + '?since_id=' + lastSeenId + '&key=' + encodeURIComponent(SYNC_API_KEY);
        const response = UrlFetchApp.fetch(url, { muteHttpExceptions: true });

        if (response.getResponseCode() !== 200) {
            throw new Error('Sync endpoint returned HTTP ' + response.getResponseCode() + ': ' + response.getContentText());
        }

        const rows = JSON.parse(response.getContentText());
        if (!rows.length) {
            return;
        }

        const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName(SHEET_NAME);
        if (!sheet) {
            throw new Error('Sheet "' + SHEET_NAME + '" not found');
        }

        let headers = getHeaders(sheet);
        if (headers.length === 0) {
            headers = FIXED_COLUMNS.slice();
            sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
        }

        let maxId = lastSeenId;

        rows.forEach(function (row) {
            headers = appendMissingHeaders(sheet, headers, Object.keys(row.payload || {}));

            const rowValues = headers.map(function (header) {
                if (header === 'created_at') return row.created_at;
                if (FIXED_COLUMNS.indexOf(header) !== -1) return row[header] != null ? row[header] : '';
                const value = row.payload ? row.payload[header] : undefined;
                return value != null ? value : '';
            });

            sheet.appendRow(rowValues);

            if (row.id > maxId) {
                maxId = row.id;
            }
        });

        props.setProperty('last_seen_id', String(maxId));
    } catch (err) {
        // Don't advance last_seen_id — next run retries the same batch.
        console.error('RSVP sync failed: ' + err);
    }
}

function getHeaders(sheet) {
    const lastCol = sheet.getLastColumn();
    if (lastCol === 0) return [];
    return sheet.getRange(1, 1, 1, lastCol).getValues()[0].filter(function (h) {
        return h !== '';
    });
}

function appendMissingHeaders(sheet, headers, newKeys) {
    const missing = newKeys.filter(function (key) {
        return headers.indexOf(key) === -1;
    });
    if (missing.length === 0) return headers;

    const updated = headers.concat(missing);
    sheet.getRange(1, headers.length + 1, 1, missing.length).setValues([missing]);
    return updated;
}
