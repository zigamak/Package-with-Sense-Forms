<?php
/**
 * PackageWithSense RSVP backend config.
 * DB credentials and the sync API key come from environment variables.
 *
 * Also loads a local `.env` file (KEY=VALUE per line, no quotes) from this same
 * folder if one exists, for hosts where setting real env vars per-site isn't
 * convenient (e.g. XAMPP locally, some shared-hosting cPanel setups). Real
 * environment variables always win over `.env`. `.env` must never be committed
 * — see README.md.
 */

declare(strict_types=1);

function pws_load_dotenv(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        [$key, $value] = $parts;
        $key = trim($key);
        $value = trim($value);
        if (getenv($key) === false) {
            putenv("$key=$value");
        }
    }
}

pws_load_dotenv(__DIR__ . '/.env');

function pws_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

define('PWS_DB_HOST', pws_env('PWS_DB_HOST', 'localhost'));
define('PWS_DB_NAME', pws_env('PWS_DB_NAME', 'packagewithsense_rsvp'));
define('PWS_DB_USER', pws_env('PWS_DB_USER', 'root'));
define('PWS_DB_PASS', pws_env('PWS_DB_PASS', ''));

// Root URL each form page is served from — no trailing slash. Pages use this
// (not relative paths) to build the <form action>, canonical links, and the
// submit/upload endpoints, so a page keeps working regardless of how deep
// WordPress or the local dev server mounts it.
//
// This can't be auto-detected reliably: on localhost/XAMPP the project lives
// under a subfolder (http://localhost/packagewithsense_forms), but in
// production it's the bare domain (https://packagewithsense.com) once these
// folders sit at the WordPress root — the difference is a path prefix that
// isn't safely inferable from the request. Set PWS_SITE_URL explicitly per
// environment in .env.
define('PWS_SITE_URL', rtrim(pws_env('PWS_SITE_URL', 'http://localhost/packagewithsense_forms'), '/'));

// Used to authorize the Google Apps Script sync endpoint (get-new.php).
// Generate a real value with e.g. `php -r "echo bin2hex(random_bytes(24));"`
// and set it via env var or .env — do not leave the placeholder in production.
define('SYNC_API_KEY', pws_env('SYNC_API_KEY', 'CHANGE_ME_32_CHAR_RANDOM_STRING'));

// Known form_slug values, shared by submit.php, note-submit.php, and
// get-notes.php. Add a new slug here when a new form page is created.
const PWS_ALLOWED_FORMS = ['trad-white', 'white-only', 'eto-60', 'ayodele-elochukwu'];

// Error logging: PHP's own errors/warnings/fatals go to logs/php-error.log
// (log_errors, not display_errors — never show raw errors to guests filling
// out the form). Set PWS_DEBUG=1 in .env to also display errors on-screen
// while developing locally.
const PWS_LOG_DIR = __DIR__ . '/logs';
const PWS_LOG_FILE = PWS_LOG_DIR . '/php-error.log';

if (!is_dir(PWS_LOG_DIR)) {
    mkdir(PWS_LOG_DIR, 0755, true);
}

ini_set('log_errors', '1');
ini_set('error_log', PWS_LOG_FILE);
ini_set('display_errors', pws_env('PWS_DEBUG', '0') === '1' ? '1' : '0');

// Use this (rather than PHP's own error_log()) for anything caught and
// handled — e.g. a failed INSERT — so it's timestamped consistently and
// lands in the same file as uncaught errors.
function pws_log(string $message): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents(PWS_LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}

function pws_pdo(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . PWS_DB_HOST . ';dbname=' . PWS_DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, PWS_DB_USER, PWS_DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}
