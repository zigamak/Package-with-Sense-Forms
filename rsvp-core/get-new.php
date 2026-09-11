<?php
/**
 * Sync endpoint polled by the Google Apps Script (apps-script.gs).
 * GET /rsvp-core/get-new.php?since_id=<int>&key=<SYNC_API_KEY>
 *
 * Read-only, append-only log semantics: this endpoint never marks rows as
 * synced. The Apps Script tracks its own last_seen_id and only advances it
 * after a successful Sheet write.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'GET only']);
    exit;
}

$key = $_GET['key'] ?? '';
if (!hash_equals(SYNC_API_KEY, (string)$key)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$sinceId = filter_input(INPUT_GET, 'since_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 0]]);

$stmt = pws_pdo()->prepare(
    'SELECT id, form_slug, full_name, email, phone, rsvp_category, guest_count, payload, created_at
     FROM rsvp_submissions
     WHERE id > :since_id
     ORDER BY id ASC
     LIMIT 500'
);
$stmt->execute(['since_id' => $sinceId]);
$rows = $stmt->fetchAll();

foreach ($rows as &$row) {
    $row['payload'] = json_decode($row['payload'], true);
}
unset($row);

echo json_encode($rows);
