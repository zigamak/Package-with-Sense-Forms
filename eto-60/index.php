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
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;0,6..96,700;0,6..96,900;1,6..96,400;1,6..96,500&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
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
    --ink:#1a2236;
    --muted:#5d6577;
    --line:rgba(201,169,97,0.28);
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    position:relative;
    background:var(--navy-deep);
    color:var(--ink);
    font-family:'Montserrat',sans-serif;
    -webkit-font-smoothing:antialiased;
  }
  .eto-wrap{max-width:1100px;margin:0 auto;}

  /* ---------- HERO ---------- */
  .eto-hero{
    position:relative;
    background:transparent;
    color:var(--white);
    text-align:center;
    padding:clamp(64px,12vw,120px) 20px clamp(56px,10vw,96px);
    overflow:hidden;
  }
  /* One continuous marbled navy field behind the whole page. Fixed so the
     veining stays put while the content scrolls over it. */
  .eto-marble{
    position:fixed;
    inset:0;
    width:100%;
    height:100%;
    pointer-events:none;
    z-index:0;
  }
  .eto-hero,
  .eto-strip,
  .eto-form-section,
  .eto-footer{
    position:relative;
    z-index:1;
  }
  .eto-hero-inner{
    position:relative;
    z-index:2;
    text-align:left;
  }

  .eto-hero-top{
    display:flex;
    flex-direction:column;
    gap:clamp(40px,8vw,64px);
  }

  /* Left stacked headline, matching the printed Save-the-Date card */
  .eto-headline{margin:0;flex:1;}
  .eto-headline .eto-std-svg{
    display:block;
    width:100%;
    max-width:min(560px,92vw);
    height:auto;
    filter:drop-shadow(0 3px 18px rgba(201,169,97,0.18));
  }

  /* Name / occasion / venue block, with the gold vertical rule from the card */
  .eto-details{
    padding-left:clamp(20px,4vw,34px);
    border-left:2px solid rgba(201,169,97,0.55);
    align-self:flex-end;
  }
  .eto-details .eto-det-svg{
    display:block;
    width:100%;
    max-width:min(330px,80vw);
    height:auto;
  }

  /* Desktop: headline and name/occasion sit on the same row */
  @media (min-width:701px){
    .eto-hero-top{
      flex-direction:row;
      align-items:flex-end;
      justify-content:space-between;
      flex-wrap:wrap;
    }
  }

  @media (max-width:700px){
    .eto-headline .eto-std-svg{
      margin:0 auto;
      max-width:min(400px,78vw);
    }
    .eto-details{
      margin:0 auto;
      border-left:none;
      padding-left:0;
      border-top:2px solid rgba(201,169,97,0.45);
      padding-top:26px;
      align-self:stretch;
    }
    .eto-details .eto-det-svg{
      margin:0 auto;
      max-width:min(300px,74vw);
    }
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
    font-family:'Bodoni Moda','Bodoni MT','Didot','Didot LT STD','Hoefler Text',Garamond,'Times New Roman',serif;
    font-size:clamp(1.5rem,5vw,2.1rem);
    font-weight:700;
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
    background:transparent;
    color:var(--white);
    padding:clamp(34px,6vw,52px) 20px;
    border-top:1px solid rgba(201,169,97,0.18);
    border-bottom:1px solid rgba(201,169,97,0.18);
  }
  .eto-strip .eto-wrap{text-align:center;}
  .eto-dress-label{
    font-family:'Montserrat',sans-serif;
    font-size:0.68rem;
    letter-spacing:0.32em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--gold);
    margin:0 0 clamp(12px,2.5vw,18px);
  }
  .eto-dress-value{
    font-family:'Bodoni Moda','Bodoni MT','Didot','Didot LT STD','Hoefler Text',Garamond,'Times New Roman',serif;
    font-variation-settings:'opsz' 72;
    font-weight:500;
    font-size:clamp(2rem,7vw,3.6rem);
    line-height:1.15;
    letter-spacing:0.01em;
    margin:0;
    background:linear-gradient(168deg,var(--gold-pale) 0%,var(--gold-light) 40%,var(--gold) 100%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
  }

  /* ---------- FORM SECTION ---------- */
  .eto-form-section{
    background:transparent;
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
    color:var(--gold);
    font-weight:600;
    margin:0 0 12px;
  }
  .eto-section-title{
    font-family:'Bodoni Moda','Bodoni MT','Didot','Didot LT STD','Hoefler Text',Garamond,'Times New Roman',serif;
    font-size:clamp(2rem,6vw,3rem);
    font-weight:700;
    letter-spacing:0.04em;
    color:var(--gold-pale);
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
    font-family:'Bodoni Moda','Bodoni MT','Didot','Didot LT STD','Hoefler Text',Garamond,'Times New Roman',serif;
    font-size:clamp(1.4rem,4vw,1.85rem);
    font-weight:700;
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
    font-family:'Bodoni Moda','Bodoni MT','Didot','Didot LT STD','Hoefler Text',Garamond,'Times New Roman',serif;
    font-style:italic;
    font-size:1rem;
    color:var(--muted);
    margin:4px 0 0;
    line-height:1.5;
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
  .eto-submit:disabled{opacity:0.75;cursor:not-allowed;}
  .eto-submit{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;
  }
  .eto-spinner{
    display:none;
    width:14px;
    height:14px;
    border:2px solid rgba(240,228,196,0.35);
    border-top-color:var(--gold-pale);
    border-radius:50%;
    animation:eto-spin .6s linear infinite;
  }
  .eto-submit.is-loading .eto-spinner{display:inline-block;}
  @keyframes eto-spin{to{transform:rotate(360deg);}}
  @media (prefers-reduced-motion:reduce){
    .eto-spinner{animation-duration:1.6s;}
  }

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
    font-family:'Bodoni Moda','Bodoni MT','Didot','Didot LT STD','Hoefler Text',Garamond,'Times New Roman',serif;
    font-size:clamp(1.6rem,5vw,2.2rem);
    font-weight:700;
    color:var(--gold-pale);
    margin:0 0 14px;
    letter-spacing:0.04em;
  }
  .eto-confirmation p{
    font-family:'Bodoni Moda','Bodoni MT','Didot','Didot LT STD','Hoefler Text',Garamond,'Times New Roman',serif;
    font-size:clamp(1.05rem,3vw,1.25rem);
    line-height:1.6;
    margin:0;
    color:rgba(255,255,255,0.94);
  }

  /* ---------- FOOTER ---------- */
  .eto-footer{
    background:transparent;
    border-top:1px solid rgba(201,169,97,0.18);
    color:rgba(255,255,255,0.62);
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

<header class="eto-hero">
  <div class="eto-hero-inner eto-wrap">
    <div class="eto-hero-top">
      <div class="eto-headline">
        <?= file_get_contents(__DIR__ . '/assets/save-the-date.svg') ?>
      </div>

      <div class="eto-details">
        <?= file_get_contents(__DIR__ . '/assets/event-details.svg') ?>
      </div>
    </div>

    <div class="eto-countdown" id="eto-countdown">
      <div class="eto-count-box"><span class="eto-count-num" id="eto-days">00</span><span class="eto-count-lbl">Days</span></div>
      <div class="eto-count-box"><span class="eto-count-num" id="eto-hours">00</span><span class="eto-count-lbl">Hours</span></div>
      <div class="eto-count-box"><span class="eto-count-num" id="eto-minutes">00</span><span class="eto-count-lbl">Minutes</span></div>
      <div class="eto-count-box"><span class="eto-count-num" id="eto-seconds">00</span><span class="eto-count-lbl">Seconds</span></div>
    </div>
  </div>
</header>

<section class="eto-strip">
  <div class="eto-wrap">
    <h3 class="eto-dress-label">Dress Code</h3>
    <p class="eto-dress-value">Elegant &amp; Glamorous</p>
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

        <fieldset class="eto-fieldset">
          <legend>Will you be attending? <span class="eto-required">*</span></legend>
          <div class="eto-radio-group">
            <label><input type="radio" name="attending" value="yes" required> Yes</label>
            <label><input type="radio" name="attending" value="no"> No</label>
          </div>
        </fieldset>

        <button type="submit" class="eto-submit" id="eto-submit">
          <span class="eto-submit-label">Submit</span>
          <span class="eto-spinner" aria-hidden="true"></span>
        </button>
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
      countdownEl.innerHTML = '<span style="font-family:\'Bodoni Moda\',Didot,serif;font-size:1.5rem;color:#f0e4c4;">Celebrating Today!</span>';
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
    submitBtn.classList.add('is-loading');

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
        submitBtn.classList.remove('is-loading');
      });
  });
})();
</script>

</body>
</html>