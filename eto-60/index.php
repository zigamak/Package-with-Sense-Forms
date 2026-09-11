<?php
declare(strict_types=1);
require_once __DIR__ . '/../rsvp-core/config.php';

const PWS_FORM_SLUG = 'eto-60';

$submitted = ($_GET['submitted'] ?? '') === '1';
$failed    = ($_GET['submitted'] ?? '') === '0';
$actionUrl = PWS_SITE_URL . '/rsvp-core/submit.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ebenezer Taiwo Olushina's 60th RSVP</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --navy:#0d1b3e;
    --navy-deep:#070f26;
    --royal:#1e3a8a;
    --royal-light:#2b4fb8;
    --gold:#c9a961;
    --gold-light:#e3cd92;
    --gold-pale:#f0e4c4;
    --white:#ffffff;
    --paper:#fbfaf7;
    --ink:#1a2236;
    --muted:#5d6577;
    --line:rgba(201,169,97,0.28);
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    background:var(--navy-deep);
    color:var(--ink);
    font-family:'Montserrat',sans-serif;
    -webkit-font-smoothing:antialiased;
  }
  .eto-wrap{max-width:1100px;margin:0 auto;}

  /* ---------- HERO ---------- */
  .eto-hero{
    position:relative;
    background:
      radial-gradient(ellipse at 20% 15%, rgba(30,58,138,0.55) 0%, transparent 55%),
      radial-gradient(ellipse at 85% 80%, rgba(30,58,138,0.4) 0%, transparent 50%),
      linear-gradient(160deg,#0d1b3e 0%,#070f26 55%,#0b1730 100%);
    color:var(--white);
    text-align:center;
    padding:clamp(64px,12vw,120px) 20px clamp(56px,10vw,96px);
    overflow:hidden;
  }
  .eto-marble{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    pointer-events:none;
    z-index:1;
  }
  .eto-hero-inner{position:relative;z-index:2;}
  .eto-sixty{
    font-family:'Cormorant Garamond',serif;
    font-weight:700;
    line-height:0.86;
    letter-spacing:-0.01em;
    font-size:clamp(5.5rem,22vw,13rem);
    margin:0;
    background:linear-gradient(168deg,var(--gold-pale) 0%,var(--gold-light) 30%,var(--gold) 58%,#9d7d38 100%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
    filter:drop-shadow(0 3px 18px rgba(201,169,97,0.18));
  }
  .eto-sixty-sub{
    font-family:'Montserrat',sans-serif;
    font-size:clamp(0.62rem,2.2vw,0.76rem);
    letter-spacing:0.42em;
    text-transform:uppercase;
    color:var(--gold);
    margin:6px 0 0;
    padding-left:0.42em;
  }
  .eto-rule{
    width:clamp(70px,14vw,130px);
    height:1px;
    background:linear-gradient(90deg,transparent,var(--gold),transparent);
    margin:clamp(26px,5vw,40px) auto;
  }
  .eto-name{
    font-family:'Montserrat',sans-serif;
    font-size:clamp(0.72rem,2.6vw,0.92rem);
    font-weight:400;
    letter-spacing:0.3em;
    text-transform:uppercase;
    color:rgba(255,255,255,0.82);
    margin:0 0 clamp(10px,2vw,16px);
    line-height:1.5;
  }
  .eto-occasion{
    font-family:'Cormorant Garamond',serif;
    font-style:italic;
    font-size:clamp(1.05rem,3vw,1.5rem);
    color:rgba(255,255,255,0.86);
    margin:0 0 clamp(28px,5vw,42px);
  }
  .eto-meta{
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    align-items:center;
    gap:10px clamp(18px,4vw,34px);
    font-family:'Montserrat',sans-serif;
    font-size:clamp(0.74rem,2.2vw,0.86rem);
    letter-spacing:0.16em;
    text-transform:uppercase;
    color:rgba(255,255,255,0.9);
  }
  .eto-meta span{white-space:nowrap;}
  .eto-meta i{
    display:inline-block;
    width:4px;height:4px;
    border-radius:50%;
    background:var(--gold);
    vertical-align:middle;
  }
  .eto-followup{
    font-family:'Cormorant Garamond',serif;
    font-style:italic;
    font-size:clamp(0.95rem,2.6vw,1.15rem);
    color:var(--gold-light);
    margin:clamp(26px,5vw,38px) 0 0;
  }

  /* ---------- COUNTDOWN ---------- */
  .eto-countdown{
    display:flex;
    justify-content:center;
    gap:clamp(10px,3vw,22px);
    margin:clamp(30px,6vw,44px) 0 0;
    flex-wrap:wrap;
  }
  .eto-count-box{
    min-width:clamp(62px,17vw,84px);
    padding:14px 8px 11px;
    border:1px solid rgba(201,169,97,0.35);
    border-radius:3px;
    background:rgba(255,255,255,0.03);
  }
  .eto-count-num{
    display:block;
    font-family:'Cormorant Garamond',serif;
    font-size:clamp(1.5rem,5vw,2.1rem);
    font-weight:600;
    color:var(--gold-pale);
    line-height:1;
  }
  .eto-count-lbl{
    display:block;
    margin-top:6px;
    font-size:0.6rem;
    letter-spacing:0.18em;
    text-transform:uppercase;
    color:rgba(255,255,255,0.6);
  }

  /* ---------- DETAILS STRIP ---------- */
  .eto-strip{
    background:var(--royal);
    color:var(--white);
    padding:clamp(34px,6vw,52px) 20px;
  }
  .eto-strip-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(190px,1fr));
    gap:clamp(22px,4vw,40px);
    text-align:center;
  }
  .eto-strip-item h3{
    font-family:'Montserrat',sans-serif;
    font-size:0.66rem;
    letter-spacing:0.2em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--gold-light);
    margin:0 0 10px;
  }
  .eto-strip-item p{
    font-family:'Cormorant Garamond',serif;
    font-size:clamp(1.05rem,2.8vw,1.3rem);
    margin:0;
    line-height:1.45;
    color:rgba(255,255,255,0.95);
  }

  /* ---------- FORM SECTION ---------- */
  .eto-form-section{
    background:var(--paper);
    padding:clamp(48px,9vw,84px) 20px clamp(60px,10vw,96px);
    scroll-margin-top:24px;
  }

  /* ---------- FIXED RSVP ANCHOR ---------- */
  .eto-rsvp-fab{
    position:fixed;
    left:50%;
    bottom:clamp(16px,3.5vw,28px);
    transform:translateX(-50%);
    z-index:60;
    display:inline-flex;
    align-items:center;
    gap:9px;
    padding:13px clamp(24px,5vw,36px);
    border-radius:999px;
    background:linear-gradient(135deg,var(--navy) 0%,var(--royal) 100%);
    border:1px solid rgba(201,169,97,0.55);
    color:var(--gold-pale);
    font-family:'Montserrat',sans-serif;
    font-size:0.72rem;
    letter-spacing:0.22em;
    text-transform:uppercase;
    font-weight:600;
    text-decoration:none;
    white-space:nowrap;
    box-shadow:0 10px 30px rgba(6,13,32,0.45);
    transition:opacity .3s, visibility .3s, transform .2s, box-shadow .25s;
  }
  .eto-rsvp-fab:hover{
    transform:translateX(-50%) translateY(-2px);
    box-shadow:0 14px 36px rgba(6,13,32,0.55);
  }
  .eto-rsvp-fab i{
    display:inline-block;
    width:5px;height:5px;
    border-right:1.5px solid var(--gold);
    border-bottom:1.5px solid var(--gold);
    transform:rotate(45deg) translate(-1px,-1px);
  }
  .eto-rsvp-fab.is-hidden{
    opacity:0;
    visibility:hidden;
    pointer-events:none;
  }
  @media (max-width:400px){
    .eto-rsvp-fab{
      font-size:0.66rem;
      letter-spacing:0.16em;
      padding:12px 22px;
    }
  }
  .eto-section-head{text-align:center;margin-bottom:clamp(28px,5vw,40px);}
  .eto-eyebrow{
    font-size:0.66rem;
    letter-spacing:0.26em;
    text-transform:uppercase;
    color:var(--royal);
    font-weight:600;
    margin:0 0 12px;
  }
  .eto-section-title{
    font-family:'Cormorant Garamond',serif;
    font-size:clamp(2rem,6vw,3rem);
    font-weight:600;
    letter-spacing:0.04em;
    color:var(--navy);
    margin:0;
  }
  .eto-card{
    max-width:660px;
    margin:0 auto;
    background:var(--white);
    border:1px solid var(--line);
    border-radius:4px;
    padding:clamp(26px,5.5vw,48px);
    box-shadow:0 18px 50px rgba(13,27,62,0.10);
  }
  .eto-card-heading{
    font-family:'Cormorant Garamond',serif;
    font-size:clamp(1.4rem,4vw,1.85rem);
    font-weight:600;
    text-align:center;
    color:var(--navy);
    margin:0 0 6px;
  }
  .eto-card-sub{
    display:block;
    text-align:center;
    font-size:0.74rem;
    letter-spacing:0.14em;
    text-transform:uppercase;
    color:var(--muted);
    margin-bottom:clamp(24px,4vw,34px);
  }

  .eto-field{margin-bottom:20px;}
  .eto-field label{
    display:block;
    font-size:0.7rem;
    letter-spacing:0.14em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--navy);
    margin-bottom:8px;
  }
  .eto-required{color:#b3452f;}
  .eto-field input[type=text],
  .eto-field input[type=tel],
  .eto-field input[type=email],
  .eto-field input[type=number],
  .eto-field select,
  .eto-field textarea{
    width:100%;
    padding:13px 14px;
    border:1px solid #d8dbe4;
    border-radius:3px;
    font-family:'Montserrat',sans-serif;
    font-size:0.92rem;
    color:var(--ink);
    background:var(--white);
    transition:border-color .2s, box-shadow .2s;
  }
  .eto-field textarea{min-height:96px;resize:vertical;}
  .eto-field input:focus,
  .eto-field select:focus,
  .eto-field textarea:focus{
    outline:none;
    border-color:var(--royal);
    box-shadow:0 0 0 3px rgba(30,58,138,0.12);
  }

  .eto-fieldset{
    border:1px solid var(--line);
    border-radius:3px;
    padding:20px 18px 6px;
    margin:0 0 22px;
  }
  .eto-fieldset legend{
    font-size:0.7rem;
    letter-spacing:0.14em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--navy);
    padding:0 8px;
  }
  .eto-radio-group{display:flex;gap:20px;flex-wrap:wrap;margin-bottom:14px;}
  .eto-radio-group label{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:0.9rem;
    letter-spacing:0;
    text-transform:none;
    font-weight:400;
    color:var(--ink);
    cursor:pointer;
    margin:0;
  }
  .eto-radio-group input[type=radio]{accent-color:var(--royal);width:16px;height:16px;}

  .eto-note{
    font-family:'Cormorant Garamond',serif;
    font-style:italic;
    font-size:1rem;
    color:var(--muted);
    margin:4px 0 0;
    line-height:1.5;
  }
  .eto-dress{
    background:linear-gradient(135deg,rgba(30,58,138,0.05),rgba(201,169,97,0.09));
    border-left:2px solid var(--gold);
    padding:16px 18px;
    margin:0 0 22px;
    border-radius:0 3px 3px 0;
  }
  .eto-dress h4{
    font-size:0.66rem;
    letter-spacing:0.2em;
    text-transform:uppercase;
    color:var(--royal);
    margin:0 0 6px;
    font-weight:600;
  }
  .eto-dress p{
    font-family:'Cormorant Garamond',serif;
    font-size:1.15rem;
    color:var(--navy);
    margin:0;
    letter-spacing:0.04em;
  }

  .eto-submit{
    width:100%;
    padding:15px 20px;
    border:none;
    border-radius:3px;
    background:linear-gradient(135deg,var(--navy) 0%,var(--royal) 100%);
    color:var(--gold-pale);
    font-family:'Montserrat',sans-serif;
    font-size:0.78rem;
    letter-spacing:0.2em;
    text-transform:uppercase;
    font-weight:600;
    cursor:pointer;
    transition:transform .15s, box-shadow .25s, opacity .2s;
  }
  .eto-submit:hover:not(:disabled){
    transform:translateY(-1px);
    box-shadow:0 10px 26px rgba(13,27,62,0.28);
  }
  .eto-submit:disabled{opacity:0.6;cursor:not-allowed;}

  .eto-form-message{
    margin:0 0 16px;
    font-size:0.86rem;
    text-align:center;
    min-height:1px;
  }
  .eto-form-message--error{color:#b3452f;}
  .eto-error-text{
    display:block;
    margin-top:6px;
    font-size:0.76rem;
    color:#b3452f;
    letter-spacing:0;
    text-transform:none;
    font-weight:400;
  }

  /* ---------- CONFIRMATION ---------- */
  .eto-confirmation{
    text-align:center;
    padding:clamp(26px,5vw,42px) clamp(18px,4vw,30px);
    background:linear-gradient(150deg,var(--navy) 0%,var(--royal) 100%);
    border-radius:4px;
    color:var(--white);
  }
  .eto-confirmation h4{
    font-family:'Cormorant Garamond',serif;
    font-size:clamp(1.6rem,5vw,2.2rem);
    font-weight:600;
    color:var(--gold-pale);
    margin:0 0 14px;
    letter-spacing:0.04em;
  }
  .eto-confirmation p{
    font-family:'Cormorant Garamond',serif;
    font-size:clamp(1.05rem,3vw,1.25rem);
    line-height:1.6;
    margin:0;
    color:rgba(255,255,255,0.94);
  }

  /* ---------- FOOTER ---------- */
  .eto-footer{
    background:var(--navy-deep);
    color:rgba(255,255,255,0.6);
    text-align:center;
    padding:clamp(30px,5vw,46px) 20px;
    font-size:0.72rem;
    letter-spacing:0.14em;
    text-transform:uppercase;
  }
  .eto-footer strong{color:var(--gold-light);font-weight:500;}
  .eto-footer a{color:var(--gold-light);text-decoration:none;}

  @media (max-width:560px){
    .eto-radio-group{gap:14px;}
  }
</style>
</head>
<body>

<header class="eto-hero">
  <svg class="eto-marble" viewBox="0 0 1200 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
    <defs>
      <linearGradient id="etoGround" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" stop-color="#12244d"/>
        <stop offset="48%" stop-color="#0a1430"/>
        <stop offset="100%" stop-color="#060d20"/>
      </linearGradient>
      <linearGradient id="etoVein" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="#1e3a8a" stop-opacity="0"/>
        <stop offset="45%" stop-color="#2b4fb8" stop-opacity="0.5"/>
        <stop offset="100%" stop-color="#1e3a8a" stop-opacity="0"/>
      </linearGradient>
      <linearGradient id="etoVeinGold" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0%" stop-color="#c9a961" stop-opacity="0"/>
        <stop offset="50%" stop-color="#c9a961" stop-opacity="0.24"/>
        <stop offset="100%" stop-color="#c9a961" stop-opacity="0"/>
      </linearGradient>
      <radialGradient id="etoGlow" cx="50%" cy="42%" r="58%">
        <stop offset="0%" stop-color="#1e3a8a" stop-opacity="0.42"/>
        <stop offset="100%" stop-color="#1e3a8a" stop-opacity="0"/>
      </radialGradient>
    </defs>
    <rect width="1200" height="800" fill="url(#etoGround)"/>
    <ellipse cx="600" cy="340" rx="620" ry="420" fill="url(#etoGlow)"/>
    <g fill="none" stroke-linecap="round">
      <path d="M-40 180 C 180 120, 320 250, 520 190 S 900 60, 1240 150" stroke="url(#etoVein)" stroke-width="42" opacity="0.55"/>
      <path d="M-40 300 C 220 250, 380 380, 640 320 S 980 210, 1240 290" stroke="url(#etoVein)" stroke-width="26" opacity="0.45"/>
      <path d="M-40 520 C 200 470, 400 600, 660 540 S 1000 430, 1240 510" stroke="url(#etoVein)" stroke-width="34" opacity="0.4"/>
      <path d="M-40 660 C 240 620, 420 740, 700 680 S 1020 580, 1240 650" stroke="url(#etoVein)" stroke-width="20" opacity="0.35"/>
      <path d="M-40 210 C 190 150, 330 280, 530 220 S 910 90, 1240 180" stroke="url(#etoVeinGold)" stroke-width="1.6"/>
      <path d="M-40 330 C 230 280, 390 410, 650 350 S 990 240, 1240 320" stroke="url(#etoVeinGold)" stroke-width="1.2"/>
      <path d="M-40 545 C 210 495, 410 625, 670 565 S 1010 455, 1240 535" stroke="url(#etoVeinGold)" stroke-width="1.4"/>
      <path d="M-40 90 C 260 40, 460 170, 760 110 S 1060 10, 1240 70" stroke="url(#etoVeinGold)" stroke-width="0.9" opacity="0.7"/>
      <path d="M-40 730 C 220 690, 450 800, 720 745 S 1040 660, 1240 720" stroke="url(#etoVeinGold)" stroke-width="1" opacity="0.6"/>
    </g>
  </svg>
  <div class="eto-hero-inner eto-wrap">
    <p class="eto-name">Ebenezer Taiwo Olushina</p>
    <h1 class="eto-sixty">60</h1>
    <p class="eto-sixty-sub">Years</p>
    <div class="eto-rule"></div>
    <p class="eto-occasion">Birthday Celebration</p>
    <div class="eto-meta">
      <span>Wednesday 14th October, 2026</span>
      <i></i>
      <span>3:00 PM</span>
      <i></i>
      <span>Lagos, Nigeria</span>
    </div>

    <div class="eto-countdown" id="eto-countdown">
      <div class="eto-count-box"><span class="eto-count-num" id="eto-days">00</span><span class="eto-count-lbl">Days</span></div>
      <div class="eto-count-box"><span class="eto-count-num" id="eto-hours">00</span><span class="eto-count-lbl">Hours</span></div>
      <div class="eto-count-box"><span class="eto-count-num" id="eto-minutes">00</span><span class="eto-count-lbl">Minutes</span></div>
      <div class="eto-count-box"><span class="eto-count-num" id="eto-seconds">00</span><span class="eto-count-lbl">Seconds</span></div>
    </div>

    <p class="eto-followup">Kindly respond below. Formal invitation to follow.</p>
  </div>
</header>

<section class="eto-strip">
  <div class="eto-wrap eto-strip-grid">
    <div class="eto-strip-item">
      <h3>Date</h3>
      <p>Wednesday<br>14th October, 2026</p>
    </div>
    <div class="eto-strip-item">
      <h3>Time</h3>
      <p>3:00 PM<br>Prompt</p>
    </div>
    <div class="eto-strip-item">
      <h3>Venue</h3>
      <p>Lagos, Nigeria<br>Details to follow</p>
    </div>
    <div class="eto-strip-item">
      <h3>Dress Code</h3>
      <p>Elegant</p>
    </div>
  </div>
</section>

<section class="eto-form-section" id="rsvp">
  <div class="eto-section-head">
    <p class="eto-eyebrow">Kindly Respond</p>
    <h2 class="eto-section-title">RSVP</h2>
  </div>

  <div class="eto-card">
    <div class="eto-confirmation" id="eto-confirmation" <?= $submitted ? '' : 'hidden' ?>>
      <h4>Thank You!</h4>
      <p>We look forward to celebrating this Milestone with you.<br>Formal invitation to follow.</p>
    </div>

    <div <?= $submitted ? 'hidden' : '' ?> id="eto-form-shell">
      <h3 class="eto-card-heading">Confirm Your Attendance</h3>
      <span class="eto-card-sub">Ebenezer Taiwo Olushina &bull; 60th Birthday</span>

      <div class="eto-dress">
        <h4>Dress Code</h4>
        <p>Elegant</p>
      </div>

      <p class="eto-form-message <?= $failed ? 'eto-form-message--error' : '' ?>" id="eto-form-message"><?= $failed ? 'Something went wrong. Please check your details and try again.' : '' ?></p>

      <form class="eto-rsvp-form" id="eto-rsvp-form" action="<?= htmlspecialchars($actionUrl, ENT_QUOTES) ?>" method="post" novalidate>
        <input type="hidden" name="form_slug" value="<?= htmlspecialchars(PWS_FORM_SLUG, ENT_QUOTES) ?>">

        <div class="eto-field" style="flex:0 0 130px;">
          <label for="title">Title</label>
          <select id="title" name="title">
            <option value="">-</option>
            <option>Mr</option><option>Mrs</option><option>Miss</option>
            <option>Ms</option><option>Dr</option><option>Chief</option>
            <option>Engr</option><option>Otunba</option><option>Prof</option>
            <option>Rev</option><option>Pastor</option><option>Alhaji</option>
            <option>Alhaja</option>
          </select>
        </div>

        <div class="eto-field">
          <label for="full_name">Name <span class="eto-required">*</span></label>
          <input type="text" id="full_name" name="full_name" required>
        </div>

        <div class="eto-field">
          <label for="phone">Phone Number <span class="eto-required">*</span></label>
          <input type="tel" id="phone" name="phone" required>
        </div>

        <button type="submit" class="eto-submit">Submit</button>
      </form>
    </div>
  </div>
</section>

<a href="#rsvp" class="eto-rsvp-fab" id="eto-rsvp-fab">RSVP <i></i></a>

<footer class="eto-footer">
  <p>Ebenezer Taiwo Olushina &bull; <strong>60th Birthday Celebration</strong> &bull; Lagos, Nigeria</p>
</footer>

<script>
(function(){
  const form = document.getElementById('eto-rsvp-form');
  const formShell = document.getElementById('eto-form-shell');
  const confirmationEl = document.getElementById('eto-confirmation');
  const messageEl = document.getElementById('eto-form-message');

  // ---------- COUNTDOWN ----------
  const eventDate = new Date('2026-10-14T15:00:00').getTime();
  const daysEl = document.getElementById('eto-days');
  const hoursEl = document.getElementById('eto-hours');
  const minutesEl = document.getElementById('eto-minutes');
  const secondsEl = document.getElementById('eto-seconds');
  const countdownEl = document.getElementById('eto-countdown');

  function updateCountdown(){
    const diff = eventDate - new Date().getTime();
    if (diff <= 0){
      countdownEl.innerHTML = '<span style="font-family:\'Cormorant Garamond\',serif;font-size:1.5rem;color:#f0e4c4;">Celebrating Today!</span>';
      return;
    }
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    daysEl.textContent = String(d).padStart(2,'0');
    hoursEl.textContent = String(h).padStart(2,'0');
    minutesEl.textContent = String(m).padStart(2,'0');
    secondsEl.textContent = String(s).padStart(2,'0');
  }
  updateCountdown();
  setInterval(updateCountdown, 1000);

  // ---------- FIXED RSVP BUTTON ----------
  // Hide the floating button whenever the form itself is on screen, and once
  // the RSVP has been submitted (there is nothing left to jump to).
  const fab = document.getElementById('eto-rsvp-fab');
  const formSection = document.getElementById('rsvp');

  function setFabHidden(hidden){
    fab.classList.toggle('is-hidden', hidden);
  }

  if (confirmationEl && !confirmationEl.hidden){
    setFabHidden(true);
  } else if ('IntersectionObserver' in window){
    new IntersectionObserver(function(entries){
      entries.forEach(function(entry){ setFabHidden(entry.isIntersecting); });
    }, { rootMargin: '-15% 0px -15% 0px' }).observe(formSection);
  }

  // ---------- SUBMIT ----------
  form.addEventListener('submit', function(e){
    e.preventDefault();
    form.querySelectorAll('.eto-error-text').forEach(function(el){ el.remove(); });
    messageEl.className = 'eto-form-message';
    messageEl.textContent = '';
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    fetch(form.action, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: new FormData(form),
    })
      .then(function(res){ return res.json(); })
      .then(function(data){
        if (data.success){
          formShell.hidden = true;
          confirmationEl.hidden = false;
          setFabHidden(true);
          confirmationEl.scrollIntoView({ behavior:'smooth', block:'center' });
        } else {
          messageEl.className = 'eto-form-message eto-form-message--error';
          messageEl.textContent = 'Please fix the highlighted fields and try again.';
          const errors = (data && data.errors) || {};
          Object.keys(errors).forEach(function(field){
            const input = form.querySelector('[name="' + field + '"]');
            const text = document.createElement('span');
            text.className = 'eto-error-text';
            text.textContent = errors[field];
            if (input && input.closest('.eto-field')){
              input.closest('.eto-field').appendChild(text);
            } else if (input && input.closest('.eto-fieldset')){
              input.closest('.eto-fieldset').appendChild(text);
            }
          });
        }
      })
      .catch(function(){
        messageEl.className = 'eto-form-message eto-form-message--error';
        messageEl.textContent = 'Something went wrong. Please try again.';
      })
      .finally(function(){
        submitBtn.disabled = false;
      });
  });
})();
</script>

</body>
</html>
