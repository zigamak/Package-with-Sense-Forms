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

// Where the git checkout lives. cPanel's Git Version Control refuses to
// init/clone into a non-empty directory, and the live folder already holds
// .env, uploads/ and logs/ -- so the repo is cloned OUTSIDE the web root and
// its tracked files are copied into place after each pull.
const REPO_PATH     = '/home/adeisfjg/repos/pws-forms';

// The live directory actually served by Apache.
const LIVE_PATH     = '/home/adeisfjg/packagewithsense.com/forms';

const DEPLOY_BRANCH = 'main';

// Never copied over the live site: runtime data that only exists on the
// server and must survive every deploy.
const DEPLOY_EXCLUDES = ['.git', '.env', 'uploads', 'logs'];

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

if (!is_dir(REPO_PATH . '/.git')) {
    http_response_code(500);
    pws_log('deploy.php: no git checkout at ' . REPO_PATH);
    echo "No git checkout at " . REPO_PATH . " -- clone it first (see README)\n";
    exit;
}

// 1. Update the checkout that lives outside the web root.
$pull = 'cd ' . escapeshellarg(REPO_PATH)
      . ' && git fetch --quiet origin ' . escapeshellarg(DEPLOY_BRANCH)
      . ' && git reset --hard ' . escapeshellarg('origin/' . DEPLOY_BRANCH)
      . ' 2>&1';

exec($pull, $pullOut, $pullStatus);
$pullLog = implode("\n", $pullOut);
pws_log('deploy.php: pull exit=' . $pullStatus . ' :: ' . $pullLog);

if ($pullStatus !== 0) {
    http_response_code(500);
    echo "Pull failed (exit {$pullStatus}):\n{$pullLog}\n";
    exit;
}

// 2. Copy tracked files into the live directory. No --delete: a file removed
// from the repo is left alone on the server rather than risking the removal
// of something the repo does not know about.
$excludeArgs = '';
foreach (DEPLOY_EXCLUDES as $exclude) {
    $excludeArgs .= ' --exclude=' . escapeshellarg($exclude);
}

$sync = 'rsync -a' . $excludeArgs . ' '
      . escapeshellarg(rtrim(REPO_PATH, '/') . '/') . ' '
      . escapeshellarg(rtrim(LIVE_PATH, '/') . '/')
      . ' 2>&1';

exec($sync, $syncOut, $syncStatus);
$syncLog = implode("\n", $syncOut);
pws_log('deploy.php: sync exit=' . $syncStatus . ' :: ' . $syncLog);

if ($syncStatus !== 0) {
    // Deliberately no cp fallback: cp honours no --exclude, so it would copy
    // .git into the web root and expose the full repo history over HTTP.
    // Failing loudly is safer than silently deploying a less safe way.
    http_response_code(500);
    echo "Sync failed (exit {$syncStatus}):\n{$syncLog}\n"
       . "If rsync is unavailable on this host, use the cron fallback (see README).\n";
    exit;
}

echo "Deployed {$ref}:\n{$pullLog}\n{$syncLog}\n";
