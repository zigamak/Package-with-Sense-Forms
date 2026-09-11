<?php
/**
 * GitHub push webhook -> git pull on the server.
 *
 * Deployed at: https://packagewithsense.com/forms/deploy.php
 * Repo checkout: /home/adeisfjg/packagewithsense.com/forms
 *
 * The webhook secret is read from rsvp-core/.env (DEPLOY_SECRET) so it is
 * never committed. Set it there and in the GitHub webhook config.
 *
 * If exec() is disabled on this host, this script will report that clearly
 * rather than failing silently -- use the cron fallback documented in the
 * README instead.
 */

declare(strict_types=1);

require_once __DIR__ . '/rsvp-core/config.php';

header('Content-Type: text/plain');

// Absolute path to the git checkout. Must match where the repo was cloned.
const DEPLOY_PATH   = '/home/adeisfjg/packagewithsense.com/forms';
const DEPLOY_BRANCH = 'main';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "POST only\n";
    exit;
}

$secret = pws_env('DEPLOY_SECRET', '');
if ($secret === '') {
    http_response_code(500);
    pws_log('deploy.php: DEPLOY_SECRET is not set in .env');
    echo "Deploy secret not configured\n";
    exit;
}

$payload   = file_get_contents('php://input');
$received  = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected  = 'sha256=' . hash_hmac('sha256', $payload, $secret);

// hash_equals is timing-safe; an absent header compares against '' and fails.
if (!hash_equals($expected, $received)) {
    http_response_code(403);
    pws_log('deploy.php: bad signature from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    echo "Invalid signature\n";
    exit;
}

// Only deploy on a push to the target branch -- ignore tags, other branches,
// and GitHub's initial "ping" event.
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
if ($event === 'ping') {
    echo "pong\n";
    exit;
}
if ($event !== 'push') {
    echo "Ignoring event: {$event}\n";
    exit;
}

$body = json_decode($payload, true);
$ref  = $body['ref'] ?? '';
if ($ref !== 'refs/heads/' . DEPLOY_BRANCH) {
    echo "Ignoring ref: {$ref}\n";
    exit;
}

if (!function_exists('exec')) {
    http_response_code(500);
    pws_log('deploy.php: exec() is disabled on this host; use the cron fallback');
    echo "exec() is disabled -- use the cron fallback (see README)\n";
    exit;
}

$cmd = 'cd ' . escapeshellarg(DEPLOY_PATH)
     . ' && git fetch --quiet origin ' . escapeshellarg(DEPLOY_BRANCH)
     . ' && git reset --hard ' . escapeshellarg('origin/' . DEPLOY_BRANCH)
     . ' 2>&1';

exec($cmd, $output, $status);

$log = implode("\n", $output);
pws_log('deploy.php: pull exit=' . $status . ' :: ' . $log);

if ($status !== 0) {
    http_response_code(500);
    echo "Deploy failed (exit {$status}):\n{$log}\n";
    exit;
}

echo "Deployed {$ref}:\n{$log}\n";
