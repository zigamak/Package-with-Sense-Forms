<?php
/**
 * Public guestbook submission handler — "Leave a SWeetLove note and/or a
 * prayer for us!" Deliberately separate from submit.php/rsvp_submissions:
 * leaving a note doesn't require RSVPing, and notes get displayed back on
 * the page, so this never touches phone/email contact details.
 *
 * Required fields: form_slug, full_name, note.
 * Same JSON-or-redirect response pattern as submit.php, so a form page can
 * use the same fetch()-with-no-JS-fallback approach for both forms.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

const PWS_NOTE_MAX_LENGTH = 2000;

function pws_note_wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
}

function pws_note_respond(int $httpStatus, array $body): void
{
    http_response_code($httpStatus);
    header('Content-Type: application/json');
    echo json_encode($body);
    exit;
}

function pws_note_redirect_to_referer(string $flag): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $target = $referer !== '' ? $referer : PWS_SITE_URL . '/';
    $target = strtok($target, '?');
    header('Location: ' . $target . '?' . $flag);
    exit;
}

function pws_note_fail(array $errors): void
{
    if (pws_note_wants_json()) {
        pws_note_respond(422, ['success' => false, 'errors' => $errors]);
    }
    pws_note_redirect_to_referer('note_submitted=0');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    pws_note_respond(405, ['success' => false, 'errors' => ['_' => 'POST only']]);
}

$formSlug = trim((string)($_POST['form_slug'] ?? ''));
$fullName = trim((string)($_POST['full_name'] ?? ''));
$note     = trim((string)($_POST['note'] ?? ''));

$errors = [];

if (!in_array($formSlug, PWS_ALLOWED_FORMS, true)) {
    $errors['form_slug'] = 'Unknown form.';
}
if ($fullName === '') {
    $errors['full_name'] = 'Name is required.';
}
if ($note === '') {
    $errors['note'] = 'Please write a note.';
} elseif (mb_strlen($note) > PWS_NOTE_MAX_LENGTH) {
    $errors['note'] = 'Please keep your note under ' . PWS_NOTE_MAX_LENGTH . ' characters.';
}

if ($errors) {
    pws_note_fail($errors);
}

try {
    $stmt = pws_pdo()->prepare(
        'INSERT INTO guest_notes (form_slug, full_name, note) VALUES (:form_slug, :full_name, :note)'
    );
    $stmt->execute([
        'form_slug' => $formSlug,
        'full_name' => $fullName,
        'note'      => $note,
    ]);
} catch (PDOException $e) {
    pws_log('note-submit.php insert failed (form_slug=' . $formSlug . '): ' . $e->getMessage());
    if (pws_note_wants_json()) {
        pws_note_respond(500, ['success' => false, 'errors' => ['_' => 'Server error, please try again.']]);
    }
    pws_note_redirect_to_referer('note_submitted=0');
}

if (pws_note_wants_json()) {
    pws_note_respond(200, [
        'success' => true,
        'note' => ['full_name' => $fullName, 'note' => $note],
    ]);
}

pws_note_redirect_to_referer('note_submitted=1');
