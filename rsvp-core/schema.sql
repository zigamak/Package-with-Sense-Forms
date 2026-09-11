-- PackageWithSense RSVP backend schema
-- Two shared tables, reused across forms and future client projects — not
-- just this wedding.
--
-- rsvp_submissions: standard columns are kept deliberately generic — things
-- almost any RSVP form needs to filter/export/headcount on. Anything specific
-- to *this event's shape* — e.g. "attending_trad" / "attending_white", which
-- only make sense because Sinmi & Warami have two ceremonies (a future
-- client's form might have one, three, or none) — lives in `payload` JSON
-- instead, exactly like the Asoebi item/quantity/delivery choices already do.
-- That keeps this table reusable without a schema change per event.
CREATE TABLE IF NOT EXISTS rsvp_submissions (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    form_slug       VARCHAR(30)  NOT NULL,            -- e.g. 'trad-white', 'white-only'
    full_name       VARCHAR(150) NOT NULL,
    email           VARCHAR(150),
    phone           VARCHAR(30)  NOT NULL,
    rsvp_category   VARCHAR(60),                      -- e.g. 'Jackson Family', 'Adepoju Friends/Guests', 'Bridal Party'
    guest_count     VARCHAR(20),                      -- 'just_me' | 'plus_one' | 'family'
    payload         JSON         NOT NULL,            -- attending_trad, attending_white, Asoebi choices, guest names,
                                                        -- payment method, proof-of-payment URL — all event/form-specific
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_form_slug (form_slug),
    INDEX idx_phone (phone),
    INDEX idx_rsvp_category (rsvp_category)
);

-- guest_notes: a standalone, publicly-displayable guestbook — "Leave a
-- SWeetLove note and/or a prayer for us!" — deliberately NOT part of
-- rsvp_submissions. Leaving a note doesn't require RSVPing, and the whole
-- point is these get shown back on the page, so keeping it a separate table
-- means it can be queried/displayed without touching guest contact details
-- (phone/email) at all. form_slug links a note back to whichever form page
-- it was left on, same convention as rsvp_submissions, so this table is
-- reusable as-is for any future form/event.
CREATE TABLE IF NOT EXISTS guest_notes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    form_slug  VARCHAR(30)  NOT NULL,
    full_name  VARCHAR(150) NOT NULL,
    note       TEXT         NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_form_slug (form_slug)
);
