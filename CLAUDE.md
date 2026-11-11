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
   `FORM_SLUG` matching the page's slug and one `COLUMNS` entry per form field.
5. A row in the README tables.

## Apps Scripts: always, for every form folder

- **Every folder with an `index.php` gets its own `<folder>/<folder>.gs`.** That includes
  redirect stubs left by renames (e.g. `dara-justice/`), which sync the old slug.
- Whenever you add, change or rename a form, write or update its `.gs` in the same
  change. If a form's fields change, update its `COLUMNS` and `VALUE_LABELS` too.
- Before finishing any task here, check that every form folder has its `.gs`, and write
  any that are missing, without being asked.
- `SYNC_API_KEY` is the same key for every form, but it is only pasted in the Apps Script
  editor (see Rules).

## Rules

- `SYNC_API_KEY` in `.gs` files stays `PASTE_SYNC_API_KEY_HERE`. The repo is public.
- Never commit `rsvp-core/.env`, `uploads/` or `logs/`.
- Deploys never delete files, so renaming a folder leaves the old page live on the server.
  Replace the old `index.php` with a 301 redirect to the new folder (see
  `dara-justice/index.php`), which also drops it from the forms index, and keep the old
  slug in `PWS_ALLOWED_FORMS`.
- Stage explicit paths. Never `git add .`.
