<?php
declare(strict_types=1);
require_once __DIR__ . '/../rsvp-core/config.php';

const PWS_FORM_SLUG = 'trad-white';

$submitted     = ($_GET['submitted'] ?? '') === '1';
$failed        = ($_GET['submitted'] ?? '') === '0';
$noteSubmitted = ($_GET['note_submitted'] ?? '') === '1';
$actionUrl     = PWS_SITE_URL . '/rsvp-core/submit.php';
$noteActionUrl = PWS_SITE_URL . '/rsvp-core/note-submit.php';

$notesStmt = pws_pdo()->prepare(
    'SELECT full_name, note, created_at FROM guest_notes WHERE form_slug = :form_slug ORDER BY created_at DESC LIMIT 200'
);
$notesStmt->execute(['form_slug' => PWS_FORM_SLUG]);
$guestNotes = $notesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sinmi & Warami – Wedding</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@400;600&family=Montserrat:wght@300;500;700&display=swap');

    :root {
      --primary: #BF5B30;      /* burnt orange */
      --accent: #C89B3C;       /* mustard */
      --cream: #FBF3E7;
      --menu-bg: #FBF3E7;
      --countdown-color: #8A5A2B;
      --ink: #5A3E2B;
    }

    html, body {
      margin: 0; padding: 0; box-sizing: border-box;
      font-family: 'Playfair Display', serif;
      background: var(--cream);
      color: #fff;
      height: 100%;
    }
    *, *::before, *::after { box-sizing: inherit; }

    /* ---------- TOP NAMES + DATE ---------- */
    .sw2-top-info {
      text-align: center;
      background: transparent;
      color: var(--ink);
      padding: 40px 20px 25px;
      border-bottom: 1px solid rgba(90,62,43,.08);
      position: relative;
      z-index: 5;
    }
    .sw2-couple-names {
      font-family: 'Great Vibes', cursive;
      font-size: clamp(2.5rem, 7vw, 5rem);
      color: var(--primary);
      margin: 0;
      line-height: 1.2;
    }
    .sw2-date {
      font-size: 1.1rem;
      letter-spacing: 2px;
      color: var(--accent);
      margin-top: 8px;
    }
    .sw2-hashtag {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.72rem;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      color: var(--ink);
      opacity: 0.65;
      margin-top: 10px;
    }

    /* ---------- NAVBAR ---------- */
    .sw2-nav {
      display: flex; justify-content: center; align-items: center;
      padding: 14px 40px; background: #ffffff;
      border-bottom: 1px solid rgba(0,0,0,0.1);
      position: relative; z-index: 10;
    }
    .sw2-logo-top {
      width: 100px; height: auto;
      position: absolute; left: 40px; top: 8px;
    }
    .sw2-nav .sw2-logo-top { display: none; }
    .sw2-menu {
      list-style: none; display: flex; gap: 40px; margin: 0; padding: 0;
    }
    .sw2-menu a {
      text-decoration: none; color: var(--primary); font-weight: 600;
      letter-spacing: 0.5px; transition: color .3s;
      font-family: 'Montserrat', sans-serif; font-size: 0.85rem;
      text-transform: uppercase;
    }
    .sw2-menu a:hover { color: var(--accent); }
    .sw2-menu-toggle {
      display: none; font-size: 14px; color: var(--primary);
      cursor: pointer; position: absolute; right: 20px;
      font-family: 'Montserrat', sans-serif; font-weight: 600;
      padding: 8px 16px; border: 1px solid var(--primary);
      border-radius: 4px; background: transparent;
      transition: all 0.3s ease;
    }
    .sw2-menu-toggle:hover { background: var(--primary); color: #ffffff; }

    /* ---------- MOBILE DRAWER ---------- */
    .sw2-drawer {
      position: fixed; top: 0; left: 0;
      width: 78%; max-width: 360px; height: 100%;
      background: var(--menu-bg); transform: translateX(-100%);
      transition: transform .3s ease; z-index: 200;
      padding: 20px; display: flex; flex-direction: column; gap: 10px;
      color: var(--ink);
    }
    .sw2-drawer.is-open { transform: translateX(0); }
    .sw2-drawer-header {
      display: flex; align-items: center; justify-content: space-between;
      padding-bottom: 10px; border-bottom: 1px solid rgba(90,62,43,.12);
    }
    .sw2-drawer-title {
      margin: 0; font-size: 1.1rem; color: var(--ink); letter-spacing: .5px;
      font-family: 'Montserrat', sans-serif;
    }
    .sw2-drawer-cancel {
      appearance: none; border: 1px solid var(--primary);
      padding: 6px 12px; border-radius: 999px; background: transparent;
      color: var(--primary); font-family: 'Montserrat', sans-serif;
      font-weight: 600; cursor: pointer;
    }
    .sw2-drawer ul {
      list-style: none; padding: 0; margin: 14px 0 0;
      display: flex; flex-direction: column; gap: 18px;
    }
    .sw2-drawer a {
      color: var(--primary); text-decoration: none; font-size: 1.05rem;
      font-family: 'Montserrat', sans-serif; letter-spacing: .3px;
    }
    .sw2-drawer a:hover { color: var(--accent); }
    .sw2-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.5);
      opacity: 0; visibility: hidden; transition: opacity .3s ease;
      z-index: 150;
    }
    .sw2-overlay.is-open { opacity: 1; visibility: visible; }

    /* ---------- HERO / SLIDER ---------- */
    .sw2-hero {
      position: relative;
      min-height: 100vh; height: 100vh;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      text-align: center;
      overflow: hidden; margin: 0; padding: 0;
    }
    .sw2-bg { position: absolute; inset: 0; z-index: 0; overflow: hidden; }
    .sw2-track {
      display: flex; height: 100%; will-change: transform;
      animation: sw2-marquee 60s linear infinite;
    }
    .sw2-seq { display: flex; height: 100%; }
    .sw2-slide {
      flex: 0 0 auto; width: 33.333vw; height: 100%;
      background-size: cover; background-position: center top;
      position: relative; margin: 0;
    }
    .sw2-slide::before {
      content: ""; position: absolute; inset: 0;
      background: rgba(40, 26, 14, 0.5);
    }
    .s1, .s3, .s5 { background-image: url('https://packagewithsense.com/wp-content/uploads/2026/07/cover-couple-beach.jpg'); }
    .s2, .s4, .s6 { background-image: url('https://packagewithsense.com/wp-content/uploads/2026/07/save-the-date-photo.jpg'); }

    @keyframes sw2-marquee {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }

    .sw2-announce {
      position: relative; z-index: 1;
      font-family: 'Great Vibes', cursive;
      font-weight: 400; letter-spacing: 1.2px;
      margin: 0 0 20px;
      color: var(--accent);
      font-size: clamp(1.8rem, 4.5vw, 3.5rem);
      text-shadow: 0 0 12px rgba(200,155,60,0.8), 0 2px 10px rgba(0,0,0,0.5);
      margin-top: 2.5rem;
    }

    .sw2-countdown {
      position: relative; z-index: 1;
      display: flex; justify-content: center; gap: 15px;
      font-family: 'Montserrat', sans-serif; font-weight: 600;
      margin-top: 10px;
    }
    .sw2-countdown span {
      background: #ffffff;
      color: var(--countdown-color);
      padding: 10px 16px; border-radius: 8px;
      min-width: 60px; text-align: center;
      font-size: clamp(1rem, 2.5vw, 1.3rem);
      border: 1.5px solid var(--countdown-color);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      backdrop-filter: blur(4px);
    }
    .sw2-countdown .label {
      display: block; font-size: 0.7rem; margin-top: 4px;
      color: var(--countdown-color); font-weight: 600;
      text-transform: uppercase; letter-spacing: 1px;
    }

    /* ---------- CONTENT SECTIONS ---------- */
    .sw2-section {
      max-width: 900px; margin: 0 auto;
      padding: 70px 24px;
      text-align: center;
      color: var(--ink);
    }
    .sw2-section-eyebrow {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.68rem; letter-spacing: 0.35em; text-transform: uppercase;
      color: var(--primary); margin: 0 0 14px;
    }
    .sw2-section-title {
      font-family: 'Great Vibes', cursive;
      font-size: clamp(2rem, 5vw, 3rem);
      color: var(--accent); margin: 0 0 40px; line-height: 1;
    }

    /* Wedding detail cards */
    .sw2-cards {
      display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
      text-align: left;
    }
    .sw2-card {
      background: #ffffff;
      border: 1px solid rgba(200,155,60,0.35);
      border-radius: 6px;
      padding: 30px 26px;
      box-shadow: 0 8px 28px rgba(90,62,43,0.08);
    }
    .sw2-card h3 {
      font-family: 'Great Vibes', cursive;
      font-size: 1.7rem; color: var(--primary);
      margin: 0 0 18px; text-align: center;
    }
    .sw2-card-row {
      display: flex; justify-content: space-between; gap: 12px;
      padding: 10px 0; border-bottom: 1px solid rgba(200,155,60,0.18);
      font-family: 'Montserrat', sans-serif; font-size: 0.92rem;
    }
    .sw2-card-row:last-child { border-bottom: none; }
    .sw2-card-row .k {
      text-transform: uppercase; letter-spacing: 0.12em; font-size: 0.68rem;
      color: var(--primary); font-weight: 700; white-space: nowrap;
    }
    .sw2-card-row .v {
      font-family: 'Playfair Display', serif; font-style: italic;
      color: var(--ink); text-align: right;
    }

    /* Dress code */
    .sw2-swatches {
      display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;
      margin-top: 26px;
    }
    .sw2-swatch { text-align: center; font-family: 'Montserrat', sans-serif; font-size: 0.66rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--ink); }
    .sw2-swatch-dot { width: 46px; height: 46px; border-radius: 50%; margin: 0 auto 8px; border: 3px solid #fff; box-shadow: 0 4px 14px rgba(90,62,43,0.2); }

    /* Family */
    .sw2-family-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 30px;
      margin-top: 10px;
    }
    .sw2-family-col h4 {
      font-family: 'Montserrat', sans-serif; font-size: 0.68rem;
      letter-spacing: 0.28em; text-transform: uppercase; color: var(--primary);
      margin: 0 0 10px;
    }
    .sw2-family-col p {
      font-family: 'Playfair Display', serif; font-style: italic;
      font-size: 1.1rem; color: var(--ink); margin: 0; line-height: 1.6;
    }

    /* ---------- RSVP SECTION ---------- */
    .sw2-rsvp-section { background: #fff; padding-bottom: 90px; }
    .sw2-form-card {
      background: rgba(255,253,248,0.95);
      border: 1px solid rgba(200,155,60,0.45);
      border-radius: 6px;
      padding: 40px 34px 46px;
      max-width: 620px; margin: 0 auto;
      text-align: left;
      box-shadow: 0 4px 32px rgba(90,62,43,0.09);
    }
    .sw2-form-heading {
      font-family: 'Great Vibes', cursive;
      font-size: 2rem; color: var(--primary);
      text-align: center; margin: 0 0 6px;
    }
    .sw2-form-sub {
      font-family: 'Montserrat', sans-serif; font-size: 0.66rem;
      font-weight: 500; letter-spacing: 0.26em; text-transform: uppercase;
      color: var(--ink); opacity: 0.7; text-align: center; margin: 0 0 28px; display: block;
    }

    /* ---- RSVP form fields (replaces the old Forminator overrides) ---- */
    .sw2-field { margin-bottom: 22px; }
    .sw2-field-row { display: flex; gap: 14px; }
    .sw2-field-row .sw2-field { flex: 1; }
    .sw2-form-card label,
    .sw2-form-card legend {
      display: block; font-family: 'Montserrat', sans-serif; font-size: 0.72rem;
      letter-spacing: 0.15em; text-transform: uppercase; color: var(--primary);
      font-weight: 600; margin-bottom: 8px;
    }
    .sw2-form-card input[type="text"],
    .sw2-form-card input[type="email"],
    .sw2-form-card input[type="tel"],
    .sw2-form-card input[type="number"],
    .sw2-form-card input[type="file"],
    .sw2-form-card select,
    .sw2-form-card textarea {
      width: 100%; background: #fff; border: 1px solid rgba(200,155,60,0.5);
      border-bottom: 2px solid var(--accent); border-radius: 0;
      color: var(--ink); font-family: 'Playfair Display', serif; font-size: 1.05rem;
      padding: 12px 14px;
    }
    .sw2-form-card textarea { font-family: 'Montserrat', sans-serif; font-size: 0.95rem; resize: vertical; min-height: 80px; }
    .sw2-fieldset {
      border: 1px solid rgba(200,155,60,0.4); border-radius: 4px;
      padding: 20px; margin-bottom: 24px;
    }
    .sw2-radio-group { display: flex; gap: 24px; flex-wrap: wrap; }
    .sw2-radio-group label {
      display: flex; align-items: center; gap: 8px;
      text-transform: none; letter-spacing: normal; font-weight: 500;
      font-family: 'Montserrat', sans-serif; font-size: 0.9rem; color: var(--ink);
      margin-bottom: 0;
    }
    .sw2-radio-group input[type="radio"] { accent-color: var(--primary); width: 16px; height: 16px; }
    .sw2-conditional { margin-top: 18px; padding-top: 18px; border-top: 1px dashed rgba(90,62,43,0.25); }
    .sw2-conditional[hidden] { display: none; }
    .sw2-form-note {
      font-family: 'Montserrat', sans-serif; font-size: 0.78rem; color: var(--ink);
      opacity: 0.75; line-height: 1.6; margin: -8px 0 22px;
    }
    .sw2-price-hint { font-size: 0.85rem; opacity: 0.7; }

    .sw2-form-card button[type="submit"] {
      background-color: var(--primary);
      background-image: linear-gradient(135deg, var(--accent), var(--primary));
      color: #ffffff;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.78rem;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      font-weight: 600;
      border: none;
      border-radius: 0;
      width: 100%;
      padding: 18px 32px;
      cursor: pointer;
      margin-top: 10px;
      display: block;
      text-align: center;
      opacity: 1;
      box-shadow: 0 4px 18px rgba(191,91,48,0.35);
    }
    .sw2-form-card button[type="submit"]:hover { opacity: 0.9; }
    .sw2-form-card button[type="submit"]:disabled { opacity: 0.6; cursor: not-allowed; }

    .sw2-form-message {
      margin-top: 18px; padding: 12px 16px; border-radius: 4px;
      font-family: 'Montserrat', sans-serif; font-size: 0.85rem; text-align: center; display: none;
    }
    .sw2-form-message--error { display: block; background: rgba(191,91,48,0.1); color: var(--primary); border: 1px solid var(--primary); }
    .sw2-error-text {
      display: block; color: var(--primary); font-size: 0.72rem; margin-top: 4px;
      text-transform: none; letter-spacing: normal; font-weight: 500;
      font-family: 'Montserrat', sans-serif;
    }
    .sw2-rsvp-form[hidden] { display: none; }

    .sw2-confirmation { text-align: center; padding: 20px 0; }
    .sw2-confirmation[hidden] { display: none; }
    .sw2-confirmation h4 {
      font-family: 'Great Vibes', cursive; font-size: 2.2rem; color: var(--primary); margin: 0 0 12px;
    }
    .sw2-confirmation p {
      font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.05rem; color: var(--ink);
    }

    /* ---------- SWEETLOVE NOTES (public guestbook) ---------- */
    .sw2-notes-section { background: #fff; padding-top: 0; }
    .sw2-notes-card {
      background: rgba(255,253,248,0.95);
      border: 1px solid rgba(200,155,60,0.45);
      border-radius: 6px;
      padding: 34px 34px 38px;
      max-width: 620px; margin: 0 auto;
      text-align: left;
      box-shadow: 0 4px 32px rgba(90,62,43,0.09);
    }
    .sw2-notes-form { display: flex; flex-direction: column; gap: 14px; margin-bottom: 30px; }
    .sw2-notes-form input[type="text"],
    .sw2-notes-form textarea {
      width: 100%; background: #fff; border: 1px solid rgba(200,155,60,0.5);
      border-bottom: 2px solid var(--accent); border-radius: 0;
      color: var(--ink); font-family: 'Playfair Display', serif; font-size: 1.05rem;
      padding: 12px 14px;
    }
    .sw2-notes-form textarea { font-family: 'Montserrat', sans-serif; font-size: 0.95rem; resize: vertical; min-height: 70px; }
    .sw2-notes-form button[type="submit"] {
      background-color: var(--primary);
      background-image: linear-gradient(135deg, var(--accent), var(--primary));
      color: #ffffff; font-family: 'Montserrat', sans-serif; font-size: 0.78rem;
      letter-spacing: 0.3em; text-transform: uppercase; font-weight: 600;
      border: none; padding: 16px 32px; cursor: pointer;
      box-shadow: 0 4px 18px rgba(191,91,48,0.35);
    }
    .sw2-notes-form button[type="submit"]:disabled { opacity: 0.6; cursor: not-allowed; }
    .sw2-notes-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 18px; }
    .sw2-notes-list li {
      border-top: 1px dashed rgba(90,62,43,0.2); padding-top: 16px;
    }
    .sw2-notes-list li:first-child { border-top: none; padding-top: 0; }
    .sw2-note-name {
      font-family: 'Montserrat', sans-serif; font-size: 0.7rem; letter-spacing: 0.12em;
      text-transform: uppercase; color: var(--primary); font-weight: 700; margin-bottom: 4px;
    }
    .sw2-note-text {
      font-family: 'Playfair Display', serif; font-style: italic; font-size: 1rem;
      color: var(--ink); margin: 0; line-height: 1.6;
    }
    .sw2-notes-empty {
      font-family: 'Montserrat', sans-serif; font-size: 0.85rem; color: var(--ink); opacity: 0.6;
    }

    /* ---------- FOOTER ---------- */
    .sw2-footer {
      text-align: center; padding: 34px 20px;
      background: var(--ink); color: var(--cream);
    }
    .sw2-footer-flourish { display: block; letter-spacing: 0.3em; color: var(--accent); margin-bottom: 10px; }
    .sw2-footer p { font-family: 'Montserrat', sans-serif; font-size: 0.62rem; letter-spacing: 0.2em; text-transform: uppercase; margin: 0; opacity: 0.85; }

    /* ---------- MOBILE ---------- */
    @media (max-width: 991px) {
      .sw2-top-info { padding: 30px 16px 20px; }
      .sw2-couple-names { font-size: clamp(2.2rem, 8vw, 3.5rem); }
      .sw2-date { font-size: 0.95rem; }
      .sw2-top-info .sw2-logo-top { display: none !important; }
      .sw2-nav { justify-content: space-between; padding: 12px 20px; min-height: 70px; }
      .sw2-logo-top { position: relative; left: 0; top: 0; width: 84px; }
      .sw2-nav .sw2-logo-top { display: block; }
      .sw2-menu { display: none; }
      .sw2-menu-toggle { display: block; position: relative; right: 0; }
      .sw2-hero { min-height: 100vh; height: 100vh; padding: 0; margin: 0; }
      .sw2-track { animation: none; position: relative; }
      .sw2-seq { display: contents; }
      .sw2-slide { width: 100%; position: absolute; inset: 0; opacity: 0; transition: opacity 1.1s ease; background-position: center top; }
      .sw2-slide.is-active { opacity: 1; z-index: 1; }
      .sw2-announce { margin-bottom: 16px; margin-top: 1.5rem; }
      .sw2-countdown { gap: 10px; }
      .sw2-countdown span { padding: 8px 12px; min-width: 50px; font-size: 1rem; }
      .sw2-cards, .sw2-family-grid { grid-template-columns: 1fr; }
      .sw2-section { padding: 50px 20px; }
      .sw2-field-row { flex-direction: column; gap: 0; }
    }
    @media (max-width: 480px) {
      .sw2-nav { padding: 10px 16px; min-height: 60px; }
      .sw2-logo-top { width: 72px; }
      .sw2-menu-toggle { font-size: 13px; padding: 6px 12px; }
      .sw2-top-info { padding: 25px 14px 18px; }
      .sw2-announce { margin-top: 1rem; }
      .sw2-countdown span { padding: 6px 10px; min-width: 45px; font-size: 0.9rem; }
      .sw2-form-card { padding: 28px 20px 34px; }
    }
  </style>
</head>
<body>
<div class="sw2-banner-section">

  <!-- TOP NAMES + DATE -->
  <div class="sw2-top-info">
    <h1 class="sw2-couple-names">Sinmi &amp; Warami</h1>
    <p class="sw2-date">December 12 &amp; 21, 2026 - Warri &amp; Lagos, Nigeria</p>
    <p class="sw2-hashtag">#SWeetLove</p>
    <img class="sw2-logo-top" src="https://packagewithsense.com/wp-content/uploads/2026/07/logo-mustard-bg.png" alt="Sinmi & Warami Logo">
  </div>

  <!-- NAVBAR -->
  <nav class="sw2-nav">
    <img class="sw2-logo-top" src="https://packagewithsense.com/wp-content/uploads/2026/07/logo-mustard-bg.png" alt="Sinmi & Warami Logo">
    <ul class="sw2-menu">
      <li><a href="#wedding-details">Wedding Details</a></li>
      <li><a href="#dress-code">Dress Code</a></li>
      <li><a href="#family">Family</a></li>
      <li><a href="#rsvp-section">RSVP</a></li>
    </ul>
    <button class="sw2-menu-toggle" id="sw2-toggle" aria-label="Open Menu">MENU</button>
  </nav>

  <!-- MOBILE DRAWER -->
  <div class="sw2-overlay" id="sw2-overlay"></div>
  <aside class="sw2-drawer" id="sw2-drawer" aria-hidden="true">
    <div class="sw2-drawer-header">
      <h3 class="sw2-drawer-title">Menu</h3>
      <button class="sw2-drawer-cancel" id="sw2-drawer-cancel" aria-label="Close Menu">Cancel</button>
    </div>
    <ul>
      <li><a href="#wedding-details">Wedding Details</a></li>
      <li><a href="#dress-code">Dress Code</a></li>
      <li><a href="#family">Family</a></li>
      <li><a href="#rsvp-section">RSVP</a></li>
    </ul>
  </aside>

  <!-- HERO / SLIDER -->
  <section class="sw2-hero">
    <div class="sw2-bg">
      <div class="sw2-track" id="sw2-track">
        <div class="sw2-seq">
          <div class="sw2-slide s1"></div>
          <div class="sw2-slide s2"></div>
          <div class="sw2-slide s3"></div>
          <div class="sw2-slide s4"></div>
          <div class="sw2-slide s5"></div>
          <div class="sw2-slide s6"></div>
        </div>
        <div class="sw2-seq" aria-hidden="true">
          <div class="sw2-slide s1"></div>
          <div class="sw2-slide s2"></div>
          <div class="sw2-slide s3"></div>
          <div class="sw2-slide s4"></div>
          <div class="sw2-slide s5"></div>
          <div class="sw2-slide s6"></div>
        </div>
      </div>
    </div>

    <h2 class="sw2-announce">We're Getting Married</h2>
    <div class="sw2-countdown" id="sw2-countdown">
      <span id="sw2-days">00 <span class="label">Days</span></span>
      <span id="sw2-hours">00 <span class="label">Hours</span></span>
      <span id="sw2-minutes">00 <span class="label">Minutes</span></span>
      <span id="sw2-seconds">00 <span class="label">Seconds</span></span>
    </div>
  </section>
</div>

<!-- WEDDING DETAILS -->
<section class="sw2-section" id="wedding-details">
  <p class="sw2-section-eyebrow">Two Celebrations</p>
  <h2 class="sw2-section-title">Wedding Details</h2>
  <div class="sw2-cards">
    <div class="sw2-card">
      <h3>Traditional Wedding</h3>
      <div class="sw2-card-row"><span class="k">Date</span><span class="v">Sat, 12 Dec 2026</span></div>
      <div class="sw2-card-row"><span class="k">Time</span><span class="v">2:00 PM</span></div>
      <div class="sw2-card-row"><span class="k">Location</span><span class="v">Warri, Delta State</span></div>
      <div class="sw2-card-row"><span class="k">Dress Code</span><span class="v">Asoebi / Traditional</span></div>
    </div>
    <div class="sw2-card">
      <h3>White Wedding</h3>
      <div class="sw2-card-row"><span class="k">Date</span><span class="v">Mon, 21 Dec 2026</span></div>
      <div class="sw2-card-row"><span class="k">Time</span><span class="v">3:00 PM &ndash; 10:00 PM</span></div>
      <div class="sw2-card-row"><span class="k">Location</span><span class="v">Lekki, Lagos</span></div>
      <div class="sw2-card-row"><span class="k">Dress Code</span><span class="v">Asoebi / Black Tie</span></div>
    </div>
  </div>
</section>

<!-- DRESS CODE -->
<section class="sw2-section" id="dress-code" style="background:#fff;">
  <p class="sw2-section-eyebrow">Come Dressed To Celebrate</p>
  <h2 class="sw2-section-title">Our Colour Palette</h2>
  <p style="font-family:'Playfair Display',serif;font-style:italic;font-size:1.1rem;line-height:1.8;max-width:560px;margin:0 auto;">
    A sunset rustic theme - think warm, earthy, golden tones for both celebrations.
  </p>
  <div class="sw2-swatches">
    <div class="sw2-swatch"><div class="sw2-swatch-dot" style="background:#BF5B30;"></div>Burnt Orange</div>
    <div class="sw2-swatch"><div class="sw2-swatch-dot" style="background:#C89B3C;"></div>Mustard</div>
    <div class="sw2-swatch"><div class="sw2-swatch-dot" style="background:#7A8450;"></div>Olive Green</div>
    <div class="sw2-swatch"><div class="sw2-swatch-dot" style="background:#EDE0C8;"></div>Beige</div>
    <div class="sw2-swatch"><div class="sw2-swatch-dot" style="background:#5A3E2B;"></div>Brown</div>
    <div class="sw2-swatch"><div class="sw2-swatch-dot" style="background:#D4AF37;"></div>Gold</div>
  </div>
</section>

<!-- FAMILY -->
<section class="sw2-section" id="family">
  <p class="sw2-section-eyebrow">With Love From</p>
  <h2 class="sw2-section-title">Our Families</h2>
  <div class="sw2-family-grid">
    <div class="sw2-family-col">
      <h4>Bride's Parents</h4>
      <p>Dr. Lewis Aboyewa Jackson, Esq.<br>&amp; Mrs. Mariam Alero Jackson</p>
    </div>
    <div class="sw2-family-col">
      <h4>Groom's Parents</h4>
      <p>Mr. Olatunde Adepoju<br>&amp; Mrs. Ibitomi Adepoju</p>
    </div>
  </div>
</section>

<!-- RSVP -->
<section class="sw2-rsvp-section" id="rsvp-section">
  <div class="sw2-section" style="padding-bottom:0;">
    <p class="sw2-section-eyebrow">Kindly Respond</p>
    <h2 class="sw2-section-title">RSVP</h2>
  </div>
  <div class="sw2-form-card">
    <h3 class="sw2-form-heading">Confirm Your Attendance</h3>
    <span class="sw2-form-sub">Traditional &bull; 12 Dec &nbsp;|&nbsp; White &bull; 21 Dec 2026</span>

    <div class="sw2-confirmation" id="sw2-confirmation" <?= $submitted ? '' : 'hidden' ?>>
      <h4>Thank You!</h4>
      <p>Your RSVP has been received - see you there! 🎉<br>#SWeetLove</p>
    </div>

    <form class="sw2-rsvp-form" id="sw2-rsvp-form" action="<?= htmlspecialchars($actionUrl, ENT_QUOTES) ?>" method="post" novalidate <?= $submitted ? 'hidden' : '' ?>>
      <input type="hidden" name="form_slug" value="trad-white">

      <div class="sw2-field-row">
        <div class="sw2-field" style="flex:0 0 110px;">
          <label for="title">Title</label>
          <select id="title" name="title">
            <option value="">-</option>
            <option>Mr</option><option>Mrs</option><option>Miss</option>
            <option>Dr</option><option>Chief</option><option>Engr</option>
            <option>Otunba</option><option>Prof</option><option>Rev</option>
          </select>
        </div>
        <div class="sw2-field">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" required>
        </div>
      </div>

      <div class="sw2-field-row">
        <div class="sw2-field">
          <label for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" required>
        </div>
        <div class="sw2-field">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required>
        </div>
      </div>

      <div class="sw2-field">
        <label for="rsvp_category">How Do You Know The Couple?</label>
        <select id="rsvp_category" name="rsvp_category">
          <option value="">-</option>
          <option>Warami's Family</option>
          <option>Sinmi's Family</option>
          <option>Adepoju (Family)</option>
          <option>Adepoju Friends/Guests</option>
          <option>Jackson (Family)</option>
          <option>Jackson Friends/Guests</option>
          <option>Couples Friends</option>
          <option>Colleagues/Partners/Clients (KPMG)</option>
          <option>Colleagues (Total Energies)</option>
          <option>RCCG Lagos</option>
          <option>RCCG + SOJ Abuja</option>
          <option>VIP</option>
          <option>Bridal Party</option>
          <option>Other</option>
        </select>
      </div>

      <fieldset class="sw2-fieldset">
        <legend>Will you attend the Traditional Wedding - Sat 12 Dec 2026?</legend>
        <div class="sw2-radio-group">
          <label><input type="radio" name="attending_trad" value="yes" required> Yes</label>
          <label><input type="radio" name="attending_trad" value="no"> No</label>
        </div>
      </fieldset>

      <fieldset class="sw2-fieldset">
        <legend>Will you attend the White Wedding - Mon 21 Dec 2026?</legend>
        <div class="sw2-radio-group">
          <label><input type="radio" name="attending_white" value="yes" required> Yes</label>
          <label><input type="radio" name="attending_white" value="no"> No</label>
        </div>
      </fieldset>

      <fieldset class="sw2-fieldset">
        <legend>Number Of Guests Attending</legend>
        <div class="sw2-radio-group">
          <label><input type="radio" name="guest_count" value="just_me" required> Just me</label>
          <label><input type="radio" name="guest_count" value="plus_one"> Me + 1</label>
          <label><input type="radio" name="guest_count" value="family"> Me + Family</label>
        </div>
        <div class="sw2-conditional" hidden data-toggle="guest_count:plus_one,family">
          <div class="sw2-field">
            <label for="guest_names">Guest Name(s)</label>
            <input type="text" id="guest_names" name="guest_names" placeholder="Full name(s) of your guest(s)">
          </div>
        </div>
        <p class="sw2-form-note">Please note: children are only able to attend for select family members.</p>
      </fieldset>

      <fieldset class="sw2-fieldset">
        <legend>Traditional Wedding Asoebi - Would You Like To Purchase?</legend>
        <div class="sw2-radio-group">
          <label><input type="radio" name="trad_asoebi_purchase" value="yes"> Yes</label>
          <label><input type="radio" name="trad_asoebi_purchase" value="no"> No</label>
        </div>
        <div class="sw2-conditional" hidden data-toggle="trad_asoebi_purchase:yes">
          <div class="sw2-field">
            <label for="trad_asoebi_item">Item</label>
            <select id="trad_asoebi_item" name="trad_asoebi_item">
              <option value="damask_3yd">Damask fabric - 3 yards <span class="sw2-price-hint">(₦45,000)</span></option>
              <option value="damask_4yd">Damask fabric - 4 yards (₦60,000)</option>
              <option value="cotton_senator">Cotton senator - 4 yards (₦40,000)</option>
              <option value="head_piece">Head piece (₦18,000)</option>
              <option value="damask_gele">Damask Gele (₦9,000)</option>
              <option value="damask_fila">Damask Fila / cap (₦5,500)</option>
            </select>
          </div>
          <div class="sw2-field">
            <label for="trad_asoebi_qty">Quantity</label>
            <input type="number" id="trad_asoebi_qty" name="trad_asoebi_qty" min="1" value="1">
          </div>
          <div class="sw2-field">
            <label>Delivery Or Pickup</label>
            <div class="sw2-radio-group">
              <label><input type="radio" name="trad_asoebi_delivery" value="delivery"> Delivery</label>
              <label><input type="radio" name="trad_asoebi_delivery" value="pickup"> Pickup</label>
            </div>
          </div>
          <div class="sw2-field sw2-conditional" hidden data-toggle="trad_asoebi_delivery:delivery">
            <label for="trad_asoebi_address">Delivery Address</label>
            <textarea id="trad_asoebi_address" name="trad_asoebi_address"></textarea>
          </div>
          <p class="sw2-form-note">Pricing shown is a guide - final cost is confirmed for your family/guest category after RSVP.</p>
        </div>
      </fieldset>

      <fieldset class="sw2-fieldset">
        <legend>White Wedding Asoebi - Would You Like To Purchase?</legend>
        <div class="sw2-radio-group">
          <label><input type="radio" name="white_asoebi_purchase" value="yes"> Yes</label>
          <label><input type="radio" name="white_asoebi_purchase" value="no"> No</label>
        </div>
        <div class="sw2-conditional" hidden data-toggle="white_asoebi_purchase:yes">
          <div class="sw2-field">
            <label for="white_asoebi_item">Item</label>
            <select id="white_asoebi_item" name="white_asoebi_item">
              <option value="aso_oke_fila">Aso Oke Fila (₦5,500)</option>
              <option value="aso_oke_gele">Aso Oke Gele (₦25,000)</option>
              <option value="aso_oke_gele_ipele">Aso Oke Gele &amp; Ipele (₦45,000)</option>
            </select>
          </div>
          <div class="sw2-field">
            <label for="white_asoebi_qty">Quantity</label>
            <input type="number" id="white_asoebi_qty" name="white_asoebi_qty" min="1" value="1">
          </div>
          <div class="sw2-field">
            <label>Delivery Or Pickup</label>
            <div class="sw2-radio-group">
              <label><input type="radio" name="white_asoebi_delivery" value="delivery"> Delivery</label>
              <label><input type="radio" name="white_asoebi_delivery" value="pickup"> Pickup</label>
            </div>
          </div>
          <div class="sw2-field sw2-conditional" hidden data-toggle="white_asoebi_delivery:delivery">
            <label for="white_asoebi_address">Delivery Address</label>
            <textarea id="white_asoebi_address" name="white_asoebi_address"></textarea>
          </div>
        </div>
      </fieldset>

      <fieldset class="sw2-fieldset" id="sw2-payment-fieldset" hidden>
        <legend>Asoebi Payment</legend>
        <div class="sw2-field">
          <label for="payment_method">Payment Method</label>
          <select id="payment_method" name="payment_method">
            <option value="bank_transfer">Bank Transfer</option>
            <option value="cash_on_pickup">Cash On Pickup</option>
            <option value="other">Other</option>
          </select>
        </div>
        <p class="sw2-form-note">Bank details and the payment deadline will be shared with you directly after you submit this RSVP. Proof-of-payment upload is coming soon.</p>
      </fieldset>

      <button type="submit">Submit RSVP</button>
      <div class="sw2-form-message" id="sw2-form-message"></div>
    </form>
  </div>
</section>

<!-- SWEETLOVE NOTES -->
<section class="sw2-section sw2-notes-section" id="notes-section">
  <p class="sw2-section-eyebrow">A Word For The Couple</p>
  <h2 class="sw2-section-title">Leave A SWeetLove Note And/Or A Prayer For Us!</h2>
  <div class="sw2-notes-card">
    <form class="sw2-notes-form" id="sw2-notes-form" action="<?= htmlspecialchars($noteActionUrl, ENT_QUOTES) ?>" method="post" novalidate>
      <input type="hidden" name="form_slug" value="<?= htmlspecialchars(PWS_FORM_SLUG, ENT_QUOTES) ?>">
      <input type="text" name="full_name" placeholder="Your Name" required>
      <textarea name="note" placeholder="Your note or prayer for Sinmi &amp; Warami..." required></textarea>
      <button type="submit">Leave A Note</button>
      <div class="sw2-form-message" id="sw2-notes-message"></div>
    </form>
    <ul class="sw2-notes-list" id="sw2-notes-list">
      <?php if (!$guestNotes): ?>
        <li class="sw2-notes-empty" id="sw2-notes-empty">Be the first to leave a note!</li>
      <?php else: ?>
        <?php foreach ($guestNotes as $guestNote): ?>
          <li>
            <div class="sw2-note-name"><?= htmlspecialchars($guestNote['full_name'], ENT_QUOTES) ?></div>
            <p class="sw2-note-text"><?= nl2br(htmlspecialchars($guestNote['note'], ENT_QUOTES)) ?></p>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>
</section>

<!-- FOOTER -->
<footer class="sw2-footer">
  <span class="sw2-footer-flourish">❖ ❖ ❖</span>
  <p>Sinmi &amp; Warami &bull; #SWeetLove &bull; Warri &amp; Lagos</p>
</footer>

<script>
(function(){
  const toggle = document.getElementById('sw2-toggle');
  const drawer = document.getElementById('sw2-drawer');
  const overlay = document.getElementById('sw2-overlay');
  const cancelBtn = document.getElementById('sw2-drawer-cancel');

  function closeDrawer(){
    drawer.classList.remove('is-open');
    overlay.classList.remove('is-open');
    drawer.setAttribute('aria-hidden','true');
  }
  function openDrawer(){
    drawer.classList.add('is-open');
    overlay.classList.add('is-open');
    drawer.removeAttribute('aria-hidden');
  }
  toggle.addEventListener('click', openDrawer);
  overlay.addEventListener('click', closeDrawer);
  cancelBtn.addEventListener('click', closeDrawer);
  drawer.addEventListener('click', e => { if(e.target.tagName === 'A') closeDrawer(); });

  const slides = document.querySelectorAll('.sw2-slide');
  let i = 0;
  function initMobileFader(){
    const unique = Array.from(slides).slice(0,6);
    unique[0].classList.add('is-active');
    setInterval(() => {
      unique.forEach(s => s.classList.remove('is-active'));
      i = (i + 1) % unique.length;
      unique[i].classList.add('is-active');
    }, 5000);
  }
  if (window.innerWidth < 992) initMobileFader();

  // Countdown to the earlier (Traditional) wedding date
  const weddingDate = new Date('2026-12-12T00:00:00').getTime();
  const countdownEl = document.getElementById('sw2-countdown');
  const daysEl = document.getElementById('sw2-days');
  const hoursEl = document.getElementById('sw2-hours');
  const minutesEl = document.getElementById('sw2-minutes');
  const secondsEl = document.getElementById('sw2-seconds');

  function updateCountdown() {
    const now = new Date().getTime();
    const diff = weddingDate - now;
    if (diff <= 0) {
      countdownEl.innerHTML = '<span style="font-size:1.2rem; color:var(--countdown-color);">Happily Married!</span>';
      return;
    }
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    daysEl.firstChild.textContent = days.toString().padStart(2, '0');
    hoursEl.firstChild.textContent = hours.toString().padStart(2, '0');
    minutesEl.firstChild.textContent = minutes.toString().padStart(2, '0');
    secondsEl.firstChild.textContent = seconds.toString().padStart(2, '0');
  }
  updateCountdown();
  setInterval(updateCountdown, 1000);

  let lastIsMobile = window.innerWidth < 992;
  window.addEventListener('resize', () => {
    const isMobile = window.innerWidth < 992;
    if (isMobile !== lastIsMobile) location.reload();
  });

  // ---------- RSVP FORM BEHAVIOR ----------
  const form = document.getElementById('sw2-rsvp-form');
  const confirmationEl = document.getElementById('sw2-confirmation');
  const messageEl = document.getElementById('sw2-form-message');
  const paymentFieldset = document.getElementById('sw2-payment-fieldset');

  function applyToggle(el) {
    const [radioName, valuesCsv] = el.getAttribute('data-toggle').split(':');
    const values = valuesCsv.split(',');
    const checked = form.querySelector('input[name="' + radioName + '"]:checked');
    const show = !!checked && values.indexOf(checked.value) !== -1;
    el.hidden = !show;
    el.querySelectorAll('input, select, textarea').forEach(function (field) {
      field.disabled = !show;
    });
  }

  function updatePaymentVisibility() {
    const tradYes = form.querySelector('input[name="trad_asoebi_purchase"]:checked');
    const whiteYes = form.querySelector('input[name="white_asoebi_purchase"]:checked');
    const show = (tradYes && tradYes.value === 'yes') || (whiteYes && whiteYes.value === 'yes');
    paymentFieldset.hidden = !show;
  }

  form.querySelectorAll('[data-toggle]').forEach(function (el) {
    const radioName = el.getAttribute('data-toggle').split(':')[0];
    form.querySelectorAll('input[name="' + radioName + '"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        applyToggle(el);
        updatePaymentVisibility();
      });
    });
    applyToggle(el);
  });
  updatePaymentVisibility();

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    form.querySelectorAll('.sw2-error-text').forEach(function (el) { el.remove(); });
    messageEl.className = 'sw2-form-message';
    messageEl.textContent = '';
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    fetch(form.action, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: new FormData(form),
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          form.hidden = true;
          confirmationEl.hidden = false;
          confirmationEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
          messageEl.className = 'sw2-form-message sw2-form-message--error';
          messageEl.textContent = 'Please fix the highlighted fields and try again.';
          const errors = (data && data.errors) || {};
          Object.keys(errors).forEach(function (field) {
            const input = form.querySelector('[name="' + field + '"]');
            const text = document.createElement('span');
            text.className = 'sw2-error-text';
            text.textContent = errors[field];
            if (input && input.closest('.sw2-field')) {
              input.closest('.sw2-field').appendChild(text);
            }
          });
        }
      })
      .catch(function () {
        messageEl.className = 'sw2-form-message sw2-form-message--error';
        messageEl.textContent = 'Something went wrong. Please try again.';
      })
      .finally(function () {
        submitBtn.disabled = false;
      });
  });

  // ---------- SWEETLOVE NOTES BEHAVIOR ----------
  const notesForm = document.getElementById('sw2-notes-form');
  const notesList = document.getElementById('sw2-notes-list');
  const notesMessage = document.getElementById('sw2-notes-message');

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  notesForm.addEventListener('submit', function (e) {
    e.preventDefault();
    notesForm.querySelectorAll('.sw2-error-text').forEach(function (el) { el.remove(); });
    notesMessage.className = 'sw2-form-message';
    notesMessage.textContent = '';
    const submitBtn = notesForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    fetch(notesForm.action, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: new FormData(notesForm),
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          const empty = document.getElementById('sw2-notes-empty');
          if (empty) empty.remove();
          const li = document.createElement('li');
          li.innerHTML = '<div class="sw2-note-name">' + escapeHtml(data.note.full_name) + '</div>' +
            '<p class="sw2-note-text">' + escapeHtml(data.note.note).replace(/\n/g, '<br>') + '</p>';
          notesList.insertBefore(li, notesList.firstChild);
          notesForm.reset();
        } else {
          notesMessage.className = 'sw2-form-message sw2-form-message--error';
          notesMessage.textContent = 'Please fix the highlighted fields and try again.';
          const errors = (data && data.errors) || {};
          Object.keys(errors).forEach(function (field) {
            const input = notesForm.querySelector('[name="' + field + '"]');
            const text = document.createElement('span');
            text.className = 'sw2-error-text';
            text.textContent = errors[field];
            if (input && input.parentNode) {
              input.parentNode.insertBefore(text, input.nextSibling);
            }
          });
        }
      })
      .catch(function () {
        notesMessage.className = 'sw2-form-message sw2-form-message--error';
        notesMessage.textContent = 'Something went wrong. Please try again.';
      })
      .finally(function () {
        submitBtn.disabled = false;
      });
  });
})();
</script>
</body>
</html>
