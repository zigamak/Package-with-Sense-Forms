# Package-with-Sense-Forms

Custom PHP RSVP pages for packagewithsense.com. README.md is the full guide; read it
before adding or changing a form. Pushing to `main` deploys to production automatically
(https://packagewithsense.com/forms/<folder>/).

## Every new form page must have

1. Its own folder. **The folder name is the public URL**; use lowercase, since the
   server is case-sensitive.
2. `const PWS_FORM_SLUG` at the top of `index.php`, with that slug added to
   `PWS_ALLOWED_FORMS` in `rsvp-core/config.php`. Without it `submit.php` rejects every
   RSVP.
3. **A link preview.** Call `pws_share_meta()` from `rsvp-core/share.php` in `<head>`,
   and add an `assets/share.jpg` (1200x630 JPG, under 300KB) in the event's colours. Without
   it, a link shared on WhatsApp shows only the bare domain. See "Link previews" in
   README.md.
4. An Apps Script at `<folder>/<folder>.gs`, copied from an existing one, with
   `FORM_SLUG` matching the page's slug.
5. A row in the README tables.

## Rules

- `SYNC_API_KEY` in `.gs` files stays `PASTE_SYNC_API_KEY_HERE`. The repo is public.
- Never commit `rsvp-core/.env`, `uploads/` or `logs/`.
- Deploys never delete files, so renaming a folder leaves the old page live on the server.
  Replace the old `index.php` with a 301 redirect to the new folder (see
  `dara-justice/index.php`), which also drops it from the forms index, and keep the old
  slug in `PWS_ALLOWED_FORMS`.
- Stage explicit paths. Never `git add .`.
