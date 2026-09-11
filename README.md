# PackageWithSense RSVP Backend — Sinmi & Warami Wedding

Custom PHP + MySQL RSVP backend replacing Forminator. Two shared tables back any number
of RSVP form pages; a Google Apps Script polls a read-only sync endpoint every few minutes
and appends new rows to a Google Sheet.

## Layout

```
packagewithsense_forms/            (WordPress root in production)
  .gitignore                       ignores .env, uploads/, logs/
  rsvp-core/                       shared backend — not a page, never a WP slug
    config.php                     DB connection, PWS_SITE_URL, SYNC_API_KEY, PWS_ALLOWED_FORMS
    submit.php                     generic RSVP POST handler used by every form
    note-submit.php                public guestbook POST handler ("SWeetLove notes")
    get-new.php                    read-only RSVP sync endpoint for the Apps Script (key-protected)
    get-notes.php                  read-only guestbook endpoint (public, no key — meant to be displayed)
    schema.sql                     rsvp_submissions + guest_notes tables
    apps-script.gs                 generic reference Apps Script (all forms -> "RSVPs" tab)
    apps-script-sinmi-warami.gs            no slug filter -> "sinmi-warami" tab
    apps-script-sinmi-warami-trad-white.gs filters 'trad-white' -> "sinmi-warami-trad-white" tab
    uploads/                       proof-of-payment files (locked down, gitignored)
    logs/                          php-error.log (locked down, gitignored)
    .htaccess                      blocks direct access to config/schema/.env
    .env.example                   template — copy to .env and fill in
    .env                           local secrets (gitignored, not committed)
  sinmi-warami-trad-white/
    index.php                      full landing page + Traditional+White RSVP + notes wall
  warami-sinmi/
    index.php                      full landing page + White-only RSVP + notes wall
```

Each form page is a **self-contained landing page** (hero slider, countdown, wedding
details, dress code, family section, RSVP form) adapted from the designs already built
for the site — there's deliberately no shared CSS/JS file forcing the two pages to look
alike; each `index.php` owns its full design, inline `<style>`, and inline `<script>`.
`rsvp-core/` is backend-only and has no design opinions.

Each page declares its own slug once, at the top of its `index.php`:

```php
const PWS_FORM_SLUG = 'white-only';
```

That constant drives the guestbook query and the notes form's hidden input. Slugs must be
listed in `PWS_ALLOWED_FORMS` (`rsvp-core/config.php`), currently:

```php
const PWS_ALLOWED_FORMS = ['trad-white', 'white-only'];
```

| Page | `PWS_FORM_SLUG` | Sheet tab |
|---|---|---|
| `sinmi-warami-trad-white/` | `trad-white` | `sinmi-warami-trad-white` |
| `warami-sinmi/` | `white-only` | `sinmi-warami` (unfiltered — see below) |

## Why real folders, not `/forms/`

WordPress intercepts every URL through its own catch-all rewrite rule, but that rule only
fires when **no real file or directory matches the request**
(`RewriteCond %{REQUEST_FILENAME} !-f` / `!-d`). Dropping `sinmi-warami-trad-white/` and
`warami-sinmi/` as real directories at the WordPress root means:

- `packagewithsense.com/warami-sinmi/` serves `warami-sinmi/index.php` directly via
  Apache's normal directory-index handling — WordPress never sees the request.
- **You do not need to touch WordPress's own `.htaccess`** (which WP regenerates on every
  permalink flush, silently discarding hand-added rules) or set up any reverse proxy.
- No `/forms/` prefix, no rewrite rules — the clean URL is just where the folder lives.

Note the folder name is the URL. `warami-sinmi/` serves `/warami-sinmi/` even though its
slug, sheet tab, and Apps Script are all named `sinmi-warami` — renaming the folder
changes the public URL, nothing else.

## Setup

### 1. Database

```
mysql -u root -p < rsvp-core/schema.sql
```

Adjust the database name to match your `PWS_DB_NAME` env var.

### 2. Environment variables

`rsvp-core/config.php` reads these from real environment variables, or from a `.env` file
in `rsvp-core/` (same directory, `KEY=VALUE` per line, no quotes). Copy
`rsvp-core/.env.example` to `rsvp-core/.env` and fill it in. **`.env` is gitignored —
never commit it.**

```
PWS_SITE_URL=http://localhost/packagewithsense_forms   # see note below
PWS_DB_HOST=localhost
PWS_DB_NAME=packagewithsense_rsvp
PWS_DB_USER=your_db_user
PWS_DB_PASS=your_db_password
SYNC_API_KEY=<random 32+ char string>
PWS_DEBUG=0
```

Generate a strong `SYNC_API_KEY` with:

```
php -r "echo bin2hex(random_bytes(24));"
```

**`PWS_SITE_URL`** is the root URL each page is served from, no trailing slash. Pages use
it — not relative paths — to build the `<form action>`, so a page keeps working
regardless of how deep it's mounted. It can't be auto-detected reliably (the scheme+host
is visible from the request, but the path prefix isn't), so set it explicitly per
environment:

| Environment | `PWS_SITE_URL` |
|---|---|
| Local XAMPP | `http://localhost/packagewithsense_forms` |
| Production (dropped into WP root) | `https://packagewithsense.com` |

**`PWS_DEBUG`** — set to `1` to also display PHP errors on-screen while developing
locally. Leave unset/`0` in production; errors are always written to
`rsvp-core/logs/php-error.log` either way (see "Error logging" below).

### 3. Apache

`rsvp-core/.htaccess` and `rsvp-core/logs/.htaccess` are already in place and work
unchanged on both XAMPP and production — they only govern files inside `rsvp-core/`, no
rewriting of page URLs is needed (see "Why real folders" above). `mod_rewrite` isn't
required here, but `AllowOverride All` (or equivalent) must be enabled for the folder so
`.htaccess` is honored.

### 4. Deploying to WordPress (production)

1. Copy `rsvp-core/`, `sinmi-warami-trad-white/`, and `warami-sinmi/` into the
   WordPress install's root (same level as `wp-config.php`).
2. Set `PWS_SITE_URL=https://packagewithsense.com` in `rsvp-core/.env` (or real env vars).
3. Ensure `rsvp-core/uploads/` and `rsvp-core/logs/` exist and are writable by PHP — they
   are gitignored, so a fresh clone won't create them.
4. Visit `https://packagewithsense.com/warami-sinmi/` — WordPress is untouched.

### 5. Google Sheet sync

1. Create/open the destination Google Sheet.
2. Extensions → Apps Script, paste the contents of the appropriate `.gs` file.
3. Set `SHEET_NAME`, `ENDPOINT_URL` (your domain + `/rsvp-core/get-new.php`), and
   `SYNC_API_KEY` (must match `rsvp-core/.env`) at the top of the script.
4. Triggers → Add Trigger → function `syncRsvps` → Time-driven → every 5 minutes.
5. First run creates the header row from the fixed columns; new `payload` keys get their
   own column appended automatically as they show up.

Which script to use:

| Script | `SHEET_NAME` | `FORM_SLUG_FILTER` |
|---|---|---|
| `apps-script.gs` | `RSVPs` | generic reference version |
| `apps-script-sinmi-warami.gs` | `sinmi-warami` | `null` — takes every form's submissions |
| `apps-script-sinmi-warami-trad-white.gs` | `sinmi-warami-trad-white` | `'trad-white'` |

`FORM_SLUG_FILTER = null` means "append every row regardless of slug", so the
`sinmi-warami` tab collects submissions from **both** pages. The
`sinmi-warami-trad-white` tab is filtered to `'trad-white'` and therefore holds only that
form's rows — trad-white RSVPs appear in both tabs by design. Set
`FORM_SLUG_FILTER = 'white-only'` in `apps-script-sinmi-warami.gs` if you'd rather each
tab hold exactly one form.

**The `SYNC_API_KEY` constant in each `.gs` file is a placeholder.** Paste the real key
(matching `rsvp-core/.env` on the server) into the Apps Script editor for each project —
it must never be committed.

The sync endpoint never marks rows as synced — it's a dumb append-only log. The Apps
Script tracks its own `last_seen_id` in `PropertiesService` and only advances it after a
batch is fully written to the Sheet, so a failed run safely retries next time.

## Deployment (GitHub -> cPanel auto-pull)

Pushing to `main` updates production automatically: GitHub fires a webhook, `deploy.php`
verifies the signature and runs `git fetch` + `git reset --hard origin/main`.

**Server details**

| | |
|---|---|
| SSH | `ssh adeisfjg@199.188.201.125 -p 21098` |
| Checkout path | `/home/adeisfjg/packagewithsense.com/forms` |
| Live URL | `https://packagewithsense.com/forms/` |
| Branch | `main` |

### One-time setup

1. **Deploy key (server -> GitHub).** On the server:
   ```
   ssh-keygen -t ed25519 -C "cpanel-deploy" -f ~/.ssh/github_deploy_key
   printf 'Host github.com\n  IdentityFile ~/.ssh/github_deploy_key\n  User git\n' >> ~/.ssh/config
   cat ~/.ssh/github_deploy_key.pub
   ```
   Paste that public key into GitHub -> Settings -> Deploy Keys. Leave *Allow write
   access* unchecked.

2. **Attach git to the existing files.** The directory already holds live files (including
   `.env` and `uploads/`, which must survive), so `git clone` into it will fail. Init in
   place instead:
   ```
   cd /home/adeisfjg/packagewithsense.com/forms
   git init
   git remote add origin git@github.com:zigamak/Package-with-Sense-Forms.git
   git fetch origin main
   git reset --hard origin/main
   ```
   `.env`, `uploads/`, and `logs/` are gitignored, so `reset --hard` leaves them untouched.

3. **Set the webhook secret.** Generate one and add it to `rsvp-core/.env` on the server:
   ```
   php -r "echo bin2hex(random_bytes(24));"
   ```
   ```
   DEPLOY_SECRET=<that value>
   ```

4. **Create the webhook.** GitHub -> Settings -> Webhooks -> Add webhook:
   payload URL `https://packagewithsense.com/forms/deploy.php`, content type
   `application/json`, the same secret, and *Just the push event*.

5. **Test.** Push a trivial change, then check GitHub -> Webhooks -> Recent Deliveries for
   a `200`. Failures are also logged to `rsvp-core/logs/php-error.log`.

### If `exec()` is disabled

Some shared cPanel plans block it. Check with:

```
php -r "echo function_exists('exec') ? 'exec OK' : 'exec DISABLED';"
```

If disabled, skip the webhook and use a cron job (cPanel -> Cron Jobs) every 5 minutes:

```
*/5 * * * * cd /home/adeisfjg/packagewithsense.com/forms && git fetch --quiet origin main && git reset --hard origin/main >> ~/deploy.log 2>&1
```

### What deployment does NOT update

**`rsvp-core/.env` is gitignored and is never touched by a push.** DB credentials,
`SYNC_API_KEY`, and `DEPLOY_SECRET` live only on the server. A credential or DNS change is
therefore always a manual edit:

```
ssh adeisfjg@199.188.201.125 -p 21098
nano /home/adeisfjg/packagewithsense.com/forms/rsvp-core/.env
```

Same for `uploads/` and `logs/` — they hold guest data and runtime output, stay out of
git, and are preserved across deploys.

## Database schema

Two shared tables, reused across forms and future client projects — not just this wedding.

### `rsvp_submissions`

Standard columns are kept deliberately generic: things almost any RSVP form needs to
filter/export/headcount on. Anything specific to *this event's shape* — e.g.
`attending_trad` / `attending_white`, which only make sense because Sinmi & Warami have
two ceremonies (a future client's form might have one, three, or none) — is **not** a real
column, exactly like the Asoebi item/quantity/delivery choices and extra guest names. All
of that lives in `payload` JSON instead, so adding a new form or field never requires a
schema change:

| Column | Real column because... |
|---|---|
| `id` | primary key, also used by the sync endpoint's `since_id` cursor |
| `form_slug` | which form/page this came from — filter per event |
| `full_name`, `email`, `phone` | searched/deduped on |
| `rsvp_category` | guest-list grouping (family/friends/colleagues/etc.) — generic to any RSVP, drives headcounts and exports |
| `guest_count` | plus-one/family headcount — generic to any RSVP |
| `payload` | everything event-specific: `attending_trad`, `attending_white`, Asoebi choices, guest names, payment method |

### `guest_notes`

A standalone public guestbook — "Leave a SWeetLove note and/or a prayer for us!" —
deliberately **not** part of `rsvp_submissions`: leaving a note doesn't require RSVPing,
and notes get displayed back on the page itself, so keeping it a separate table means the
public read endpoint (`get-notes.php`) never touches phone/email contact details.

| Column | Purpose |
|---|---|
| `id` | primary key |
| `form_slug` | links a note back to whichever form page it was left on |
| `full_name`, `note` | the guest's name and message |
| `created_at` | display order (newest first) |

## Adding a new RSVP form later

No schema or backend changes needed:

1. Create a real directory at the WordPress root matching the desired URL slug, e.g.
   `packagewithsense_forms/some-new-event/index.php`.
2. Build the page however you like — copy an existing `index.php` as a starting point, or
   design it from scratch. It's self-contained; there's no shared stylesheet to conform to.
3. Set `const PWS_FORM_SLUG = 'some-new-event';` at the top and emit it from every form's
   hidden `form_slug` input, rather than hardcoding a literal.
4. POST to `PWS_SITE_URL . '/rsvp-core/submit.php'`.
5. Add that slug to `PWS_ALLOWED_FORMS` in `rsvp-core/config.php` — shared by
   `submit.php`, `note-submit.php`, and `get-notes.php`.
6. Whatever extra fields you add beyond the standard columns automatically land in the
   `payload` JSON column and flow through to the Google Sheet.
7. Want a guestbook on the new page too? Copy the `sw2-notes-*` markup/CSS/JS block from
   an existing page — it POSTs to `note-submit.php` and reads back via a server-side query
   against `guest_notes` scoped to your new `form_slug`. No backend changes needed there
   either.

## Guestbook / SWeetLove notes

Below the RSVP form on both pages, a separate "Leave a SWeetLove note and/or a prayer for
us!" section lets anyone leave a public message — no RSVP required. It's intentionally
decoupled from the RSVP flow:

- `note-submit.php` — public POST handler, inserts into `guest_notes`. Same JSON-or-redirect
  response pattern as `submit.php`, so pages use the same fetch()-with-no-JS-fallback approach.
- `get-notes.php` — public GET endpoint (`?form_slug=...`), no `SYNC_API_KEY` needed since
  the data is meant to be shown publicly on the page.
- Each `index.php` also queries `guest_notes` directly via `pws_pdo()` to server-render the
  initial list (capped at 200, newest first), then a small inline script appends new notes
  to the list client-side after a successful submission.

## Error logging

All PHP errors/warnings/fatals, plus anything caught and handled (e.g. a failed INSERT in
`submit.php` or `note-submit.php`), are written to `rsvp-core/logs/php-error.log` —
timestamped, one line per entry. `rsvp-core/logs/.htaccess` blocks the folder from ever
being served over HTTP, since a stack trace or query fragment could leak details you don't
want public. The log itself is gitignored.

Guests never see raw PHP errors — `display_errors` is off unless `PWS_DEBUG=1` is set
locally. If an RSVP or note submission returns "Server error, please try again," check
this log file for the actual cause.

## Proof-of-payment uploads

`submit.php` handles uploads **generically** — it iterates everything in `$_FILES` rather
than hardcoding field names, so any form can add a file input and it just works with no
backend change. For each uploaded file it:

1. Validates size (max 5MB, `PWS_UPLOAD_MAX_MB`) and sniffs the real MIME type with
   `mime_content_type()` — the browser-supplied type/extension is not trusted.
   Allowed: JPG, PNG, HEIC (iPhone photos), PDF.
2. Saves it to `rsvp-core/uploads/` under a random filename (`bin2hex(random_bytes(16))`),
   so the original filename never influences what lands on disk.
3. Records the public URL in the payload as **`<field_name>_url`**.

Current file fields and the payload keys they produce:

| Form | File field | Payload key |
|---|---|---|
| trad-white | `trad_payment_evidence` | `trad_payment_evidence_url` |
| trad-white | `white_payment_evidence` | `white_payment_evidence_url` |
| white-only | `white_payment_evidence` | `white_payment_evidence_url` |

**The `_url` suffix matters for the Apps Scripts** — `RELEVANT_PAYLOAD_KEYS` must list
`white_payment_evidence_url`, not the bare field name, or the column comes out blank.

`rsvp-core/uploads/.htaccess` blocks the folder from ever executing a script, even if a
malicious file slipped past MIME validation. Uploaded files are gitignored — they're guest
data and don't belong in version control.

Two gotchas worth knowing:

- The form's `<form>` tag needs `enctype="multipart/form-data"` (both pages have it).
- If an upload exceeds PHP's `post_max_size`, PHP delivers an **empty** `$_POST` *and*
  `$_FILES` with no error flag — which would otherwise surface as a baffling "Unknown form"
  error. `submit.php` detects this case explicitly and returns a clear "upload too large"
  message. On shared hosting, check `upload_max_filesize` / `post_max_size` in cPanel →
  MultiPHP INI Editor; they're often set to 2M, below the 5MB the form advertises.
