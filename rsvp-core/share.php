<?php
/**
 * Link-preview (Open Graph / Twitter card) tags for form pages.
 *
 * WhatsApp, iMessage, Facebook, X and Slack all build the card shown under a
 * pasted link from these tags. Without them WhatsApp falls back to the bare
 * domain, which is what a shared form link looked like before this existed.
 *
 * Usage, inside <head>:
 *
 *   require_once __DIR__ . '/../rsvp-core/share.php';
 *   <?= pws_share_meta([
 *       'dir'         => __DIR__,
 *       'title'       => 'Dara & Justice — Traditional Wedding',
 *       'description' => 'Tuesday, 15th December 2026 · 2:00pm. Kindly RSVP.',
 *       'image_alt'   => 'Dara & Justice #DeeJay26',
 *   ]) ?>
 *
 * Keys:
 *   dir          required. The page's __DIR__ — its folder name is the URL.
 *   title        required. Card headline (plain text, not HTML).
 *   description  required. One line under the title.
 *   image        optional. Path relative to the page folder, or an absolute
 *                URL. Default 'assets/share.jpg'. Use a 1200x630 JPG under
 *                300KB: WhatsApp only shows the large card for an image at
 *                least ~300px wide and silently drops ones that are too heavy.
 *   image_alt    optional. Defaults to the title.
 *   image_width, image_height  optional. Default 1200 x 630.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function pws_share_meta(array $meta): string
{
    $folder = basename($meta['dir']);
    $url    = PWS_SITE_URL . '/' . $folder . '/';
    $image  = $meta['image'] ?? 'assets/share.jpg';

    if (!preg_match('#^https?://#', $image)) {
        $imageFile = $meta['dir'] . '/' . $image;
        $image     = $url . $image;
        // Crawlers cache previews by image URL, so a changed image keeps
        // showing the old one unless the URL changes with it.
        if (is_file($imageFile)) {
            $image .= '?v=' . filemtime($imageFile);
        }
    }

    $tags = [
        ['name', 'description', $meta['description']],
        ['property', 'og:type', 'website'],
        ['property', 'og:site_name', 'PackageWithSense'],
        ['property', 'og:url', $url],
        ['property', 'og:title', $meta['title']],
        ['property', 'og:description', $meta['description']],
        ['property', 'og:image', $image],
        ['property', 'og:image:secure_url', $image],
        ['property', 'og:image:width', (string)($meta['image_width'] ?? 1200)],
        ['property', 'og:image:height', (string)($meta['image_height'] ?? 630)],
        ['property', 'og:image:alt', $meta['image_alt'] ?? $meta['title']],
        ['name', 'twitter:card', 'summary_large_image'],
        ['name', 'twitter:title', $meta['title']],
        ['name', 'twitter:description', $meta['description']],
        ['name', 'twitter:image', $image],
    ];

    $html = '<link rel="canonical" href="' . htmlspecialchars($url, ENT_QUOTES) . '">' . "\n";
    foreach ($tags as [$attr, $key, $value]) {
        $html .= '  <meta ' . $attr . '="' . $key . '" content="' . htmlspecialchars($value, ENT_QUOTES) . '">' . "\n";
    }
    return $html;
}
