<?php
declare(strict_types=1);
require_once __DIR__ . '/../rsvp-core/config.php';

const PWS_FORM_SLUG = 'dara-justice';

// WordPress media URL of the DeeJay26 logo. Set to '' and a text monogram
// stands in for it.
const DJ_LOGO_URL = 'https://eventio.africa/wp-content/uploads/2026/10/DeeJay26-logo.png';

$submitted = ($_GET['submitted'] ?? '') === '1';
$failed    = ($_GET['submitted'] ?? '') === '0';
$actionUrl = PWS_SITE_URL . '/rsvp-core/submit.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dara &amp; Justice Traditional Wedding RSVP</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Great+Vibes&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --terracotta:#b4532a;
    --terracotta-deep:#8e3b1b;
    --burnt:#cc5a1e;
    --gold:#b8923f;
    --gold-light:#d9b968;
    --gold-pale:#f3e6c4;
    --ivory:#fbf7ef;
    --olive:#6b7536;
    --white:#ffffff;
    --ink:#3b2a22;
    --muted:#7a6a60;
    --line:rgba(184,146,63,0.32);
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    background:var(--white);
    color:var(--ink);
    font-family:'Montserrat',sans-serif;
    -webkit-font-smoothing:antialiased;
  }
  .dj-wrap{max-width:820px;margin:0 auto;}
  .dj-serif{font-family:'Cormorant Garamond',Garamond,'Times New Roman',serif;}

  /* ---------- HERO ---------- */
  .dj-hero{
    position:relative;
    text-align:center;
    padding:clamp(40px,8vw,72px) 20px clamp(44px,8vw,72px);
    overflow:hidden;
  }
  /* thin gold double frame around the invitation, like a printed card */
  .dj-frame{
    position:relative;
    max-width:760px;
    margin:0 auto;
    padding:clamp(34px,7vw,64px) clamp(18px,5vw,56px);
    border:1px solid var(--line);
    outline:1px solid var(--line);
    outline-offset:-10px;
  }

  .dj-logo{
    display:block;
    width:clamp(150px,34vw,220px);
    height:auto;
    margin:0 auto clamp(22px,4vw,32px);
    /* the logo has an off-white ground; multiply lets it melt into the page */
    mix-blend-mode:multiply;
  }
  .dj-logo-placeholder{
    width:clamp(120px,28vw,160px);
    aspect-ratio:1;
    margin:0 auto clamp(22px,4vw,32px);
    border:1px solid var(--gold);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Great Vibes',cursive;
    font-size:clamp(2.6rem,9vw,3.6rem);
    color:var(--gold);
    line-height:1;
  }

  .dj-families{
    font-size:clamp(0.98rem,2.6vw,1.12rem);
    line-height:1.55;
    color:var(--ink);
    margin:0;
  }
  .dj-families-lead{
    font-size:0.66rem;
    letter-spacing:0.3em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--terracotta);
    margin:0 0 14px;
  }
  .dj-families-grid{
    display:grid;
    grid-template-columns:1fr auto 1fr;
    gap:clamp(10px,3vw,24px);
    align-items:center;
  }
  .dj-family p{margin:0;}
  .dj-family .dj-nee{font-style:italic;color:var(--muted);font-size:0.92em;}
  .dj-amp{
    font-family:'Great Vibes',cursive;
    font-size:clamp(2rem,6vw,2.6rem);
    color:var(--gold);
    line-height:1;
  }
  @media (max-width:600px){
    .dj-families-grid{grid-template-columns:1fr;}
  }

  .dj-request{
    font-style:italic;
    font-size:clamp(1rem,2.8vw,1.18rem);
    color:var(--muted);
    max-width:460px;
    margin:clamp(22px,4vw,30px) auto clamp(18px,3vw,24px);
    line-height:1.55;
  }
  .dj-request strong{
    display:block;
    font-style:normal;
    font-weight:600;
    font-size:0.66rem;
    font-family:'Montserrat',sans-serif;
    letter-spacing:0.3em;
    text-transform:uppercase;
    color:var(--terracotta);
    margin-top:10px;
  }

  .dj-couple{margin:0;}
  .dj-couple-name{
    display:block;
    font-family:'Great Vibes',cursive;
    font-weight:400;
    font-size:clamp(2.3rem,8.5vw,3.8rem);
    line-height:1.15;
    color:var(--terracotta);
  }
  .dj-couple-full{
    display:block;
    font-family:'Cormorant Garamond',Garamond,serif;
    font-size:clamp(0.86rem,2.4vw,1rem);
    letter-spacing:0.22em;
    text-transform:uppercase;
    color:var(--muted);
    margin-top:2px;
  }
  .dj-couple-amp{
    display:block;
    font-family:'Great Vibes',cursive;
    font-size:clamp(1.8rem,5vw,2.3rem);
    color:var(--gold);
    margin:8px 0;
    line-height:1;
  }

  .dj-rule{
    width:min(240px,60%);
    height:1px;
    margin:clamp(26px,5vw,36px) auto;
    border:0;
    background:linear-gradient(90deg,transparent,var(--gold) 50%,transparent);
  }

  .dj-when{
    display:flex;
    justify-content:center;
    align-items:stretch;
    gap:0;
    flex-wrap:wrap;
  }
  .dj-when div{
    padding:4px clamp(16px,4vw,32px);
  }
  .dj-when div + div{border-left:1px solid var(--line);}
  @media (max-width:520px){
    .dj-when{flex-direction:column;gap:14px;}
    .dj-when div + div{border-left:none;}
  }
  .dj-when span{
    display:block;
    font-size:0.62rem;
    letter-spacing:0.28em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--gold);
    margin-bottom:6px;
  }
  .dj-when strong{
    font-family:'Cormorant Garamond',Garamond,serif;
    font-size:clamp(1.25rem,3.6vw,1.6rem);
    font-weight:600;
    color:var(--terracotta-deep);
  }

  /* ---------- COUNTDOWN ---------- */
  .dj-countdown{
    display:flex;
    justify-content:center;
    gap:clamp(8px,2.5vw,18px);
    margin:clamp(26px,5vw,36px) 0 0;
    flex-wrap:wrap;
  }
  .dj-count-box{
    min-width:clamp(60px,16vw,78px);
    padding:12px 8px 10px;
    border:1px solid var(--line);
    background:var(--ivory);
  }
  .dj-count-num{
    display:block;
    font-family:'Cormorant Garamond',Garamond,serif;
    font-size:clamp(1.5rem,5vw,2rem);
    font-weight:700;
    color:var(--terracotta);
    line-height:1;
  }
  .dj-count-lbl{
    display:block;
    margin-top:6px;
    font-size:0.58rem;
    letter-spacing:0.18em;
    text-transform:uppercase;
    color:var(--muted);
  }

  /* ---------- COLOURS OF THE DAY ---------- */
  .dj-strip{
    background:var(--ivory);
    border-top:1px solid var(--line);
    border-bottom:1px solid var(--line);
    padding:clamp(34px,6vw,52px) 20px;
    text-align:center;
  }
  .dj-strip-label{
    font-size:0.66rem;
    letter-spacing:0.3em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--gold);
    margin:0 0 clamp(18px,3vw,24px);
  }
  .dj-swatches{
    display:flex;
    justify-content:center;
    gap:clamp(18px,5vw,44px);
    flex-wrap:wrap;
    list-style:none;
    margin:0;
    padding:0;
  }
  .dj-swatches li{display:flex;flex-direction:column;align-items:center;gap:10px;}
  .dj-swatch{
    width:clamp(52px,13vw,66px);
    aspect-ratio:1;
    border-radius:50%;
    border:3px solid var(--white);
    box-shadow:0 0 0 1px var(--line), 0 6px 16px rgba(59,42,34,0.10);
  }
  .dj-swatches strong{
    font-family:'Cormorant Garamond',Garamond,serif;
    font-size:clamp(1.1rem,3vw,1.3rem);
    font-weight:600;
    color:var(--ink);
  }

  /* ---------- FORM SECTION ---------- */
  .dj-form-section{
    padding:clamp(48px,9vw,80px) 20px clamp(80px,12vw,110px);
    scroll-margin-top:24px;
  }
  .dj-section-head{text-align:center;margin-bottom:clamp(26px,5vw,36px);}
  .dj-eyebrow{
    font-size:0.66rem;
    letter-spacing:0.28em;
    text-transform:uppercase;
    color:var(--gold);
    font-weight:600;
    margin:0 0 6px;
  }
  .dj-section-title{
    font-family:'Great Vibes',cursive;
    font-weight:400;
    font-size:clamp(2.6rem,8vw,3.6rem);
    color:var(--terracotta);
    margin:0;
    line-height:1.1;
  }
  .dj-card{
    max-width:620px;
    margin:0 auto;
    background:var(--white);
    border:1px solid var(--line);
    border-top:3px solid var(--terracotta);
    padding:clamp(24px,5.5vw,46px);
    box-shadow:0 18px 50px rgba(142,59,27,0.08);
  }

  .dj-field{margin-bottom:20px;}
  .dj-field label{
    display:block;
    font-size:0.68rem;
    letter-spacing:0.14em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--terracotta-deep);
    margin-bottom:8px;
  }
  .dj-required{color:var(--burnt);}
  .dj-field input[type=text],
  .dj-field input[type=tel],
  .dj-field select{
    width:100%;
    padding:13px 14px;
    border:1px solid #e3d9cc;
    border-radius:2px;
    font-family:'Montserrat',sans-serif;
    font-size:0.92rem;
    color:var(--ink);
    background:var(--white);
    transition:border-color .2s, box-shadow .2s;
  }
  .dj-field input:focus,
  .dj-field select:focus{
    outline:none;
    border-color:var(--terracotta);
    box-shadow:0 0 0 3px rgba(180,83,42,0.14);
  }
  .dj-hint{
    display:block;
    margin-top:6px;
    font-size:0.74rem;
    color:var(--muted);
  }

  .dj-fieldset{
    border:1px solid var(--line);
    border-radius:2px;
    padding:18px 18px 4px;
    margin:0 0 22px;
  }
  .dj-fieldset legend{
    font-size:0.68rem;
    letter-spacing:0.14em;
    text-transform:uppercase;
    font-weight:600;
    color:var(--terracotta-deep);
    padding:0 8px;
  }
  .dj-radio-group{display:flex;gap:12px 24px;flex-wrap:wrap;margin-bottom:14px;}
  .dj-radio-group--stack{flex-direction:column;}
  .dj-radio-group label{
    display:flex;
    align-items:center;
    gap:9px;
    font-size:0.92rem;
    color:var(--ink);
    cursor:pointer;
    margin:0;
  }
  .dj-radio-group input[type=radio]{accent-color:var(--terracotta);width:16px;height:16px;margin:0;}

  .dj-submit{
    width:100%;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    padding:15px 20px;
    border:none;
    border-radius:2px;
    background:linear-gradient(135deg,var(--terracotta) 0%,var(--burnt) 100%);
    color:var(--white);
    font-family:'Montserrat',sans-serif;
    font-size:0.78rem;
    letter-spacing:0.2em;
    text-transform:uppercase;
    font-weight:600;
    cursor:pointer;
    transition:transform .15s, box-shadow .25s, opacity .2s;
  }
  .dj-submit:hover:not(:disabled){
    transform:translateY(-1px);
    box-shadow:0 10px 26px rgba(142,59,27,0.28);
  }
  .dj-submit:disabled{opacity:0.75;cursor:not-allowed;}
  .dj-spinner{
    display:none;
    width:14px;
    height:14px;
    border:2px solid rgba(255,255,255,0.35);
    border-top-color:var(--white);
    border-radius:50%;
    animation:dj-spin .6s linear infinite;
  }
  .dj-submit.is-loading .dj-spinner{display:inline-block;}
  @keyframes dj-spin{to{transform:rotate(360deg);}}
  @media (prefers-reduced-motion:reduce){
    .dj-spinner{animation-duration:1.6s;}
  }

  .dj-form-message{
    margin:0 0 16px;
    font-size:0.86rem;
    text-align:center;
    min-height:1px;
  }
  .dj-form-message--error{color:var(--burnt);}
  .dj-error-text{
    display:block;
    margin-top:6px;
    font-size:0.76rem;
    color:var(--burnt);
  }

  /* ---------- CONFIRMATION ---------- */
  .dj-confirmation{
    text-align:center;
    padding:clamp(28px,5vw,44px) clamp(18px,4vw,30px);
    background:linear-gradient(150deg,var(--terracotta-deep) 0%,var(--terracotta) 100%);
    color:var(--white);
  }
  .dj-confirmation h4{
    font-family:'Great Vibes',cursive;
    font-weight:400;
    font-size:clamp(2.2rem,7vw,2.8rem);
    color:var(--gold-pale);
    margin:0 0 8px;
    line-height:1.1;
  }
  .dj-confirmation p{
    font-family:'Cormorant Garamond',Garamond,serif;
    font-size:clamp(1.15rem,3vw,1.35rem);
    line-height:1.6;
    margin:0;
  }

  /* ---------- FIXED RSVP ANCHOR ---------- */
  .dj-rsvp-fab{
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
    background:linear-gradient(135deg,var(--terracotta) 0%,var(--burnt) 100%);
    border:1px solid var(--gold-light);
    color:var(--white);
    font-size:0.72rem;
    letter-spacing:0.22em;
    text-transform:uppercase;
    font-weight:600;
    text-decoration:none;
    white-space:nowrap;
    box-shadow:0 10px 30px rgba(142,59,27,0.35);
    transition:opacity .3s, visibility .3s, transform .2s, box-shadow .25s;
  }
  .dj-rsvp-fab:hover{
    transform:translateX(-50%) translateY(-2px);
    box-shadow:0 14px 36px rgba(142,59,27,0.45);
  }
  .dj-rsvp-fab i{
    display:inline-block;
    width:5px;height:5px;
    border-right:1.5px solid var(--gold-pale);
    border-bottom:1.5px solid var(--gold-pale);
    transform:rotate(45deg) translate(-1px,-1px);
  }
  .dj-rsvp-fab.is-hidden{
    opacity:0;
    visibility:hidden;
    pointer-events:none;
  }

  /* ---------- FOOTER ---------- */
  .dj-footer{
    border-top:1px solid var(--line);
    color:var(--muted);
    text-align:center;
    padding:clamp(28px,5vw,40px) 20px clamp(90px,14vw,110px);
    font-size:0.7rem;
    letter-spacing:0.14em;
    text-transform:uppercase;
  }
  .dj-footer p{margin:0;}
  .dj-footer strong{color:var(--terracotta);font-weight:600;}
</style>
</head>
<body>

<header class="dj-hero">
  <div class="dj-frame">
    <?php if (DJ_LOGO_URL !== ''): ?>
      <img class="dj-logo" src="<?= htmlspecialchars(DJ_LOGO_URL, ENT_QUOTES) ?>" alt="Dara &amp; Justice #DeeJay26">
    <?php else: ?>
      <div class="dj-logo-placeholder" role="img" aria-label="Dara &amp; Justice">DJ</div>
    <?php endif; ?>

    <div class="dj-families dj-serif">
      <p class="dj-families-lead">The families of</p>
      <div class="dj-families-grid">
        <div class="dj-family">
          <p>Otunba Abiodun O. Fagbohun</p>
          <p>Mrs Olaitan L. Fagbohun</p>
          <p class="dj-nee">(n&eacute;e Ogunkoya)</p>
        </div>
        <div class="dj-amp" aria-hidden="true">&amp;</div>
        <div class="dj-family">
          <p>Chief David B. Ojuederie</p>
          <p>Mrs Flora O. Ojuederie</p>
          <p class="dj-nee">(n&eacute;e Umukoro)</p>
        </div>
      </div>
    </div>

    <p class="dj-request dj-serif">
      request the honour of your presence at the
      <strong>Traditional Wedding Ceremony</strong>
    </p>
    <p class="dj-request dj-serif" style="margin-top:0;">of their children</p>

    <h1 class="dj-couple">
      <span class="dj-couple-name">Oluwadara</span>
      <span class="dj-couple-full">Fagbohun Oluwadara Harriet</span>
      <span class="dj-couple-amp" aria-hidden="true">&amp;</span>
      <span class="dj-couple-name">Ediri Justice</span>
      <span class="dj-couple-full">Ojuederie Ediri Justice</span>
    </h1>

    <hr class="dj-rule">

    <div class="dj-when">
      <div><span>Date</span><strong>Tuesday, 15th December 2026</strong></div>
      <div><span>Time</span><strong>2:00 pm</strong></div>
    </div>

    <div class="dj-countdown" id="dj-countdown">
      <div class="dj-count-box"><span class="dj-count-num" id="dj-days">00</span><span class="dj-count-lbl">Days</span></div>
      <div class="dj-count-box"><span class="dj-count-num" id="dj-hours">00</span><span class="dj-count-lbl">Hours</span></div>
      <div class="dj-count-box"><span class="dj-count-num" id="dj-minutes">00</span><span class="dj-count-lbl">Minutes</span></div>
      <div class="dj-count-box"><span class="dj-count-num" id="dj-seconds">00</span><span class="dj-count-lbl">Seconds</span></div>
    </div>
  </div>
</header>

<section class="dj-strip">
  <h3 class="dj-strip-label">Colours of the Day</h3>
  <ul class="dj-swatches">
    <li><span class="dj-swatch" style="background:#b4532a;"></span><strong>Terracotta</strong></li>
    <li><span class="dj-swatch" style="background:#f6efdf;"></span><strong>Ivory</strong></li>
    <li><span class="dj-swatch" style="background:#6b7536;"></span><strong>Olive</strong></li>
  </ul>
</section>

<section class="dj-form-section" id="rsvp">
  <div class="dj-section-head">
    <p class="dj-eyebrow">Kindly Respond</p>
    <h2 class="dj-section-title">RSVP</h2>
  </div>

  <div class="dj-card">
    <div class="dj-confirmation" id="dj-confirmation" <?= $submitted ? '' : 'hidden' ?>>
      <h4>Thank You</h4>
      <p>Thank you for your response.</p>
    </div>

    <div <?= $submitted ? 'hidden' : '' ?> id="dj-form-shell">
      <p class="dj-form-message <?= $failed ? 'dj-form-message--error' : '' ?>" id="dj-form-message"><?= $failed ? 'Something went wrong. Please check your details and try again.' : '' ?></p>

      <form class="dj-rsvp-form" id="dj-rsvp-form" action="<?= htmlspecialchars($actionUrl, ENT_QUOTES) ?>" method="post" novalidate>
        <input type="hidden" name="form_slug" value="<?= htmlspecialchars(PWS_FORM_SLUG, ENT_QUOTES) ?>">

        <div class="dj-field">
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

        <div class="dj-field">
          <label for="full_name">Full Name <span class="dj-required">*</span></label>
          <input type="text" id="full_name" name="full_name" autocomplete="name" required>
        </div>

        <div class="dj-field">
          <label for="phone">WhatsApp Number <span class="dj-required">*</span></label>
          <input type="tel" id="phone" name="phone" autocomplete="tel" required>
        </div>

        <fieldset class="dj-fieldset">
          <legend>Will you be attending? <span class="dj-required">*</span></legend>
          <div class="dj-radio-group">
            <label><input type="radio" name="attending" value="yes" required> Yes</label>
            <label><input type="radio" name="attending" value="no"> No</label>
          </div>
        </fieldset>

        <fieldset class="dj-fieldset">
          <legend>How are you related to the couple? <span class="dj-required">*</span></legend>
          <div class="dj-radio-group dj-radio-group--stack">
            <label><input type="radio" name="rsvp_category" value="brides_family" required> Bride's Family</label>
            <label><input type="radio" name="rsvp_category" value="grooms_family"> Groom's Family</label>
            <label><input type="radio" name="rsvp_category" value="friends_colleagues"> Friends/Colleagues</label>
            <label><input type="radio" name="rsvp_category" value="other"> Other</label>
          </div>
        </fieldset>

        <button type="submit" class="dj-submit" id="dj-submit">
          <span class="dj-submit-label">Submit</span>
          <span class="dj-spinner" aria-hidden="true"></span>
        </button>
      </form>
    </div>
  </div>
</section>

<a href="#rsvp" class="dj-rsvp-fab" id="dj-rsvp-fab">RSVP <i></i></a>

<footer class="dj-footer">
  <p>Dara &amp; Justice &bull; <strong>#DeeJay26</strong> &bull; 15.12.2026</p>
</footer>

<script>
(function(){
  const form = document.getElementById('dj-rsvp-form');
  const formShell = document.getElementById('dj-form-shell');
  const confirmationEl = document.getElementById('dj-confirmation');
  const messageEl = document.getElementById('dj-form-message');

  // ---------- COUNTDOWN ----------
  // Lagos time (WAT, UTC+1), so the count is right wherever the guest is.
  const eventDate = new Date('2026-12-15T14:00:00+01:00').getTime();
  const daysEl = document.getElementById('dj-days');
  const hoursEl = document.getElementById('dj-hours');
  const minutesEl = document.getElementById('dj-minutes');
  const secondsEl = document.getElementById('dj-seconds');
  const countdownEl = document.getElementById('dj-countdown');

  function updateCountdown(){
    const diff = eventDate - new Date().getTime();
    if (diff <= 0){
      countdownEl.innerHTML = '<span style="font-family:\'Great Vibes\',cursive;font-size:2rem;color:#b4532a;">Celebrating Today!</span>';
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
  const fab = document.getElementById('dj-rsvp-fab');
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
    form.querySelectorAll('.dj-error-text').forEach(function(el){ el.remove(); });
    messageEl.className = 'dj-form-message';
    messageEl.textContent = '';

    // submit.php only checks name and phone, so the two radio groups are
    // checked here before anything is sent.
    const missing = {};
    if (!form.querySelector('input[name="attending"]:checked')) missing.attending = 'Please let us know if you will attend.';
    if (!form.querySelector('input[name="rsvp_category"]:checked')) missing.rsvp_category = 'Please choose how you are related to the couple.';
    if (Object.keys(missing).length){
      showErrors(missing);
      return;
    }

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
          showErrors((data && data.errors) || {});
        }
      })
      .catch(function(){
        messageEl.className = 'dj-form-message dj-form-message--error';
        messageEl.textContent = 'Something went wrong. Please try again.';
      })
      .finally(function(){
        submitBtn.disabled = false;
        submitBtn.classList.remove('is-loading');
      });
  });

  function showErrors(errors){
    messageEl.className = 'dj-form-message dj-form-message--error';
    messageEl.textContent = 'Please fix the highlighted fields and try again.';
    Object.keys(errors).forEach(function(field){
      const input = form.querySelector('[name="' + field + '"]');
      const text = document.createElement('span');
      text.className = 'dj-error-text';
      text.textContent = errors[field];
      if (input && input.closest('.dj-field')){
        input.closest('.dj-field').appendChild(text);
      } else if (input && input.closest('.dj-fieldset')){
        input.closest('.dj-fieldset').appendChild(text);
      }
    });
  }
})();
</script>

</body>
</html>
