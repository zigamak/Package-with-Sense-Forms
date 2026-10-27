<?php
declare(strict_types=1);
require_once __DIR__ . '/../rsvp-core/config.php';

// This page moved to /deejay26/. Deploys never delete files, so this stub
// replaces the old page on the server and sends anyone with the old link on.
// It declares no form slug, so the forms index no longer lists it.
header('Location: ' . PWS_SITE_URL . '/deejay26/', true, 301);
exit;
