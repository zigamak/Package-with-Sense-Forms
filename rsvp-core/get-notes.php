<?php
/**
 * Public read endpoint for the guestbook — GET /rsvp-core/get-notes.php?form_slug=trad-white
 * No auth: these notes are meant to be displayed publicly on the page itself,
 * unlike get-new.php (private RSVP data, key-protected).
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'GET only']);
    exit;
}

$formSlug = trim((string)($_GET['form_slug'] ?? ''));
if (!in_array($formSlug, PWS_ALLOWED_FORMS, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Unknown form_slug']);
    exit;
}

$stmt = pws_pdo()->prepare(
    'SELECT full_name, note, created_at
     FROM guest_notes
     WHERE form_slug = :form_slug
     ORDER BY created_at DESC
     LIMIT 200'
);
$stmt->execute(['form_slug' => $formSlug]);

echo json_encode($stmt->fetchAll());
