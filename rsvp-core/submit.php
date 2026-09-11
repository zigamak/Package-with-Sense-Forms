<?php
/**
 * Generic RSVP submission handler shared by every RSVP form page.
 *
 * Required top-level fields: form_slug, full_name, phone.
 * Optional top-level fields (real columns, used for guest-list filtering/
 * headcounts): email, rsvp_category, guest_count — kept deliberately
 * generic so they make sense for any future RSVP form, not just this event.
 * ("Leave a SWeetLove note" lives in the separate guest_notes table — see
 * note-submit.php / get-notes.php — since it's a public guestbook, not part
 * of a private RSVP record.)
 * Anything event-specific (e.g. "attending_trad" / "attending_white", which
 * only make sense because this wedding has two ceremonies) is NOT a top-level
 * field on purpose: it falls through to the `payload` JSON column below,
 * alongside the Asoebi choices.
 * (Proof-of-payment upload is deferred for now — no file handling here.)
 * Everything else in $_POST is bundled as-is into the `payload` JSON column,
 * so a new form can add whatever custom fields it needs without touching
 * this file.
 *
 * Deliberately knows nothing about any individual form's design or confirmation
 * page: the JS-driven success path never leaves the page, and the no-JS
 * fallback redirects back to whichever page submitted the form (via
 * HTTP_REFERER) with `?submitted=1`, so each form page owns its own
 * confirmation markup.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

const PWS_ALLOWED_GUEST_COUNTS = ['just_me', 'plus_one', 'family'];
const PWS_TOP_LEVEL_FIELDS = [
    'form_slug', 'full_name', 'email', 'phone',
    'rsvp_category', 'guest_count',
];

const PWS_UPLOAD_MAX_MB = 5;
const PWS_UPLOAD_MAX_BYTES = PWS_UPLOAD_MAX_MB * 1024 * 1024;
const PWS_UPLOAD_DIR = __DIR__ . '/uploads';
// Keyed by real (sniffed) MIME type -> extension we save it as.
const PWS_UPLOAD_ALLOWED_MIME = [
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
    'image/heic'      => 'heic',  // iPhone photos
    'application/pdf' => 'pdf',
];

function pws_wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
}

function pws_respond(int $httpStatus, array $body): void
{
    http_response_code($httpStatus);
    header('Content-Type: application/json');
    echo json_encode($body);
    exit;
}

// No-JS fallback target: redirect back to the referring page (the form itself)
// with a success/error flag, rather than hardcoding any form's confirmation URL.
function pws_redirect_to_referer(string $flag): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $target = $referer !== '' ? $referer : PWS_SITE_URL . '/';
    $target = strtok($target, '?'); // drop any existing query string
    header('Location: ' . $target . '?' . $flag);
    exit;
}

function pws_fail(array $errors): void
{
    if (pws_wants_json()) {
        pws_respond(422, ['success' => false, 'errors' => $errors]);
    }
    pws_redirect_to_referer('submitted=0');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    pws_respond(405, ['success' => false, 'errors' => ['_' => 'POST only']]);
}

// When an upload exceeds php.ini's post_max_size, PHP hands us an EMPTY $_POST
// and $_FILES with no error flag anywhere — which would otherwise surface as a
// baffling "Unknown form" validation error. Catch it explicitly.
if (!$_POST && !$_FILES && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    pws_log('submit.php: POST exceeded post_max_size (' . ini_get('post_max_size')
        . '), CONTENT_LENGTH=' . $_SERVER['CONTENT_LENGTH']);
    pws_fail(['_' => 'Your upload was too large. Please use a file under ' . PWS_UPLOAD_MAX_MB . 'MB.']);
}

$formSlug     = trim((string)($_POST['form_slug'] ?? ''));
$fullName     = trim((string)($_POST['full_name'] ?? ''));
$email        = trim((string)($_POST['email'] ?? ''));
$phone        = trim((string)($_POST['phone'] ?? ''));
$rsvpCategory = trim((string)($_POST['rsvp_category'] ?? ''));
$guestCount   = trim((string)($_POST['guest_count'] ?? ''));

$errors = [];

if (!in_array($formSlug, PWS_ALLOWED_FORMS, true)) {
    $errors['form_slug'] = 'Unknown form.';
}
if ($fullName === '') {
    $errors['full_name'] = 'Full name is required.';
}
if ($phone === '') {
    $errors['phone'] = 'Phone number is required.';
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Enter a valid email address.';
}
if ($guestCount !== '' && !in_array($guestCount, PWS_ALLOWED_GUEST_COUNTS, true)) {
    $errors['guest_count'] = 'Invalid value.';
}

if ($errors) {
    pws_fail($errors);
}

$payload = [];
foreach ($_POST as $key => $value) {
    if (in_array($key, PWS_TOP_LEVEL_FIELDS, true)) {
        continue;
    }
    $payload[$key] = $value;
}

// Handle every uploaded file generically, whatever the field is named
// (white_payment_evidence, trad_payment_evidence, or anything a future form
// adds) — no field names hardcoded here, same spirit as the $_POST loop above.
// Each saved file records "<field_name>_url" in the payload.
foreach ($_FILES as $field => $file) {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        continue; // field left empty — not an error, uploads are optional
    }

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        pws_fail([$field => 'That file is too large (max ' . PWS_UPLOAD_MAX_MB . 'MB).']);
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        pws_log('submit.php upload error on ' . $field . ': PHP error code ' . $file['error']);
        pws_fail([$field => 'Upload failed, please try again.']);
    }
    if ($file['size'] > PWS_UPLOAD_MAX_BYTES) {
        pws_fail([$field => 'That file is too large (max ' . PWS_UPLOAD_MAX_MB . 'MB).']);
    }

    // Trust the file's actual contents, not the browser-supplied type/extension.
    $mime = mime_content_type($file['tmp_name']);
    if (!isset(PWS_UPLOAD_ALLOWED_MIME[$mime])) {
        pws_fail([$field => 'Only JPG, PNG, or PDF files are allowed.']);
    }

    if (!is_dir(PWS_UPLOAD_DIR) && !mkdir(PWS_UPLOAD_DIR, 0755, true)) {
        pws_log('submit.php could not create upload dir: ' . PWS_UPLOAD_DIR);
        pws_fail([$field => 'Could not save upload, please try again.']);
    }

    $filename = bin2hex(random_bytes(16)) . '.' . PWS_UPLOAD_ALLOWED_MIME[$mime];
    if (!move_uploaded_file($file['tmp_name'], PWS_UPLOAD_DIR . '/' . $filename)) {
        pws_log('submit.php move_uploaded_file failed for ' . $field . ' -> ' . $filename);
        pws_fail([$field => 'Could not save upload, please try again.']);
    }

    $payload[$field . '_url'] = PWS_SITE_URL . '/rsvp-core/uploads/' . $filename;
}

try {
    $stmt = pws_pdo()->prepare(
        'INSERT INTO rsvp_submissions
            (form_slug, full_name, email, phone, rsvp_category, guest_count, payload)
         VALUES
            (:form_slug, :full_name, :email, :phone, :rsvp_category, :guest_count, :payload)'
    );
    $stmt->execute([
        'form_slug'     => $formSlug,
        'full_name'     => $fullName,
        'email'         => $email !== '' ? $email : null,
        'phone'         => $phone,
        'rsvp_category' => $rsvpCategory !== '' ? $rsvpCategory : null,
        'guest_count'   => $guestCount !== '' ? $guestCount : null,
        'payload'       => json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
} catch (PDOException $e) {
    pws_log('submit.php insert failed (form_slug=' . $formSlug . '): ' . $e->getMessage());
    if (pws_wants_json()) {
        pws_respond(500, ['success' => false, 'errors' => ['_' => 'Server error, please try again.']]);
    }
    pws_redirect_to_referer('submitted=0');
}

if (pws_wants_json()) {
    pws_respond(200, ['success' => true]);
}

pws_redirect_to_referer('submitted=1');
