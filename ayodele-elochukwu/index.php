<?php
declare(strict_types=1);
require_once __DIR__ . '/../rsvp-core/config.php';

const PWS_FORM_SLUG = 'ayodele-elochukwu';

$submitted = ($_GET['submitted'] ?? '') === '1';
$failed    = ($_GET['submitted'] ?? '') === '0';
$actionUrl = PWS_SITE_URL . '/rsvp-core/submit.php';

const AE_IMG_BASE = 'https://packagewithsense.com/wp-content/uploads/2026/09/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ayodele &amp; Elochukwu Wedding RSVP</title>
  <link rel="icon" href="assets/favicon.ico" sizes="any">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
  <meta name="theme-color" content="#0f7a82">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --teal:#0f7a82;
    --teal-deep:#0a565c;
    --teal-dark:#073d42;
    --gold:#c8952b;
    --gold-light:#e0b45d;
    --gold-pale:#f2e3c0;
    --ivory:#f7f4ee;
    --ivory-warm:#fdfbf7;
    --ink:#2b3336;
    --muted:#6b7679;
    --white:#fff;
    --line:rgba(200,149,43,0.3);
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    background:var(--ivory);
    color:var(--ink);
    font-family:'Montserrat',sans-serif;
    -webkit-font-smoothing:antialiased;
  }
  .ae-wrap{max-width:1100px;margin:0 auto;}

  /* ---------- NAV ---------- */
  .ae-nav{
    position:fixed;
    top:0;left:0;right:0;
    z-index:50;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:14px clamp(16px,4vw,32px);
    background:rgba(7,61,66,0.92);
    backdrop-filter:blur(8px);
  }
  .ae-nav-brand{
    font-family:'Great Vibes',cursive;
    font-size:clamp(1.15rem,3vw,1.6rem);
    color:var(--gold-pale);
    text-decoration:none;
    white-space:nowrap;
  }
  .ae-menu-toggle{
    background:none;
    border:1px solid rgba(242,227,192,0.5);
    border-radius:3px;
    color:var(--gold-pale);
    font-family:'Montserrat',sans-serif;
    font-size:0.66rem;
    letter-spacing:0.18em;
    text-transform:uppercase;
    padding:9px 16px;
    cursor:pointer;
    transition:background .2s,color .2s;
  }
  .ae-menu-toggle:hover{background:var(--gold);border-color:var(--gold);color:var(--teal-dark);}
  .ae-overlay{
    position:fixed;inset:0;
    background:rgba(7,61,66,0.6);
    opacity:0;visibility:hidden;
    transition:opacity .3s,visibility .3s;
    z-index:55;
  }
  .ae-overlay.is-open{opacity:1;visibility:visible;}
  .ae-drawer{
    position:fixed;
    top:0;right:0;bottom:0;
    width:min(320px,86vw);
    background:var(--teal-dark);
    transform:translateX(100%);
    transition:transform .32s ease;
    z-index:60;
    padding:26px 24px;
    overflow-y:auto;
  }
  .ae-drawer.is-open{transform:translateX(0);}
  .ae-drawer-head{
    display:flex;align-items:center;justify-content:space-between;
    border-bottom:1px solid rgba(242,227,192,0.25);
    padding-bottom:16px;margin-bottom:22px;
  }
  .ae-drawer-title{
    font-family:'Great Vibes',cursive;
    font-size:1.5rem;color:var(--gold-pale);margin:0;
  }
  .ae-drawer-cancel{
    background:none;border:none;color:var(--gold-pale);
    font-size:1.6rem;line-height:1;cursor:pointer;padding:0 4px;
  }
  .ae-drawer a{
    display:block;
    color:var(--ivory);
    text-decoration:none;
    font-size:0.76rem;
    letter-spacing:0.16em;
    text-transform:uppercase;
    padding:13px 0;
    border-bottom:1px solid rgba(242,227,192,0.12);
    transition:color .2s,padding-left .2s;
  }
  .ae-drawer a:hover{color:var(--gold-light);padding-left:6px;}

  /* ---------- HERO ---------- */
  .ae-hero{
    position:relative;
    min-height:100svh;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    padding:clamp(90px,14vw,130px) 20px clamp(50px,9vw,80px);
    overflow:hidden;
  }
  .ae-slides{position:absolute;inset:0;z-index:0;}
  .ae-slide{
    position:absolute;inset:0;
    background-size:cover;
    background-position:center;
    opacity:0;
    transition:opacity 1.6s ease-in-out;
  }
  .ae-slide.is-active{opacity:1;}
  .ae-slides::after{
    content:'';
    position:absolute;inset:0;
    background:linear-gradient(180deg,rgba(7,61,66,0.72) 0%,rgba(7,61,66,0.55) 45%,rgba(7,61,66,0.85) 100%);
  }
  .ae-hero-inner{position:relative;z-index:2;width:100%;}
  .ae-logo{
    width:clamp(170px,38vw,268px);
    height:auto;
    aspect-ratio:1/1;
    border-radius:50%;
    box-shadow:0 14px 44px rgba(0,0,0,0.32);
    margin:0 auto clamp(24px,5vw,38px);
    display:block;
  }
  .ae-announce{
    font-family:'Great Vibes',cursive;
    font-size:clamp(1.5rem,4.2vw,2.6rem);
    color:var(--gold-light);
    margin:0 0 10px;
    text-shadow:0 2px 14px rgba(0,0,0,0.45);
  }
  .ae-names{
    font-family:'Great Vibes',cursive;
    font-size:clamp(2.6rem,9vw,5.4rem);
    color:var(--white);
    margin:0 0 14px;
    line-height:1.08;
    text-shadow:0 3px 18px rgba(0,0,0,0.5);
  }
  .ae-date{
    font-family:'Montserrat',sans-serif;
    font-size:clamp(0.72rem,2.4vw,0.9rem);
    letter-spacing:0.28em;
    text-transform:uppercase;
    color:var(--gold-pale);
    margin:0 0 clamp(26px,5vw,38px);
  }
  .ae-countdown{
    display:flex;justify-content:center;flex-wrap:wrap;
    gap:clamp(9px,2.6vw,18px);
    margin-bottom:clamp(26px,5vw,36px);
  }
  .ae-count-box{
    min-width:clamp(62px,17vw,86px);
    padding:13px 8px 10px;
    border:1px solid rgba(242,227,192,0.35);
    border-radius:3px;
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(3px);
  }
  .ae-count-num{
    display:block;
    font-family:'Playfair Display',serif;
    font-size:clamp(1.4rem,4.6vw,2rem);
    font-weight:600;
    color:var(--white);
    line-height:1;
  }
  .ae-count-lbl{
    display:block;margin-top:6px;
    font-size:0.58rem;letter-spacing:0.2em;text-transform:uppercase;
    color:rgba(255,255,255,0.72);
  }
  .ae-hero-cta{
    display:inline-flex;align-items:center;gap:9px;
    padding:14px clamp(26px,5vw,38px);
    border-radius:999px;
    background:linear-gradient(135deg,var(--gold) 0%,var(--gold-light) 100%);
    color:var(--teal-dark);
    font-size:0.72rem;letter-spacing:0.22em;text-transform:uppercase;font-weight:600;
    text-decoration:none;
    box-shadow:0 10px 28px rgba(0,0,0,0.28);
    transition:transform .2s,box-shadow .25s;
  }
  .ae-hero-cta:hover{transform:translateY(-2px);box-shadow:0 14px 34px rgba(0,0,0,0.36);}

  /* ---------- SECTION SHELL ---------- */
  .ae-section{padding:clamp(52px,9vw,90px) 20px;}
  .ae-section--tint{background:var(--ivory-warm);}
  .ae-section--teal{background:var(--teal-dark);color:var(--ivory);}
  .ae-section-head{text-align:center;margin-bottom:clamp(30px,5vw,46px);}
  .ae-eyebrow{
    font-size:0.64rem;letter-spacing:0.3em;text-transform:uppercase;
    color:var(--gold);font-weight:600;margin:0 0 12px;
  }
  .ae-section--teal .ae-eyebrow{color:var(--gold-light);}
  .ae-title{
    font-family:'Great Vibes',cursive;
    font-size:clamp(2.1rem,6.5vw,3.4rem);
    color:var(--teal-deep);
    margin:0;font-weight:400;line-height:1.15;
  }
  .ae-section--teal .ae-title{color:var(--gold-pale);}
  .ae-lead{
    max-width:640px;margin:18px auto 0;
    font-size:0.95rem;line-height:1.85;color:var(--muted);
    font-weight:300;text-align:center;
  }
  .ae-section--teal .ae-lead{color:rgba(247,244,238,0.82);}

  /* ---------- EVENT CARDS ---------- */
  .ae-events{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:clamp(18px,3vw,28px);
  }
  .ae-event{
    background:var(--white);
    border:1px solid var(--line);
    border-radius:8px;
    padding:clamp(24px,4vw,34px);
    text-align:center;
    box-shadow:0 10px 34px rgba(15,122,130,0.07);
  }
  .ae-event h3{
    font-family:'Playfair Display',serif;
    font-size:clamp(1.15rem,3vw,1.4rem);
    color:var(--teal-deep);margin:0 0 6px;font-weight:600;
  }
  .ae-event .ae-event-sub{
    font-size:0.62rem;letter-spacing:0.22em;text-transform:uppercase;
    color:var(--gold);font-weight:600;margin:0 0 18px;
  }
  .ae-event p{margin:0 0 6px;font-size:0.9rem;line-height:1.7;color:var(--muted);font-weight:300;}
  .ae-event strong{color:var(--ink);font-weight:500;}

  /* ---------- GALLERY ---------- */
  .ae-gallery{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:clamp(10px,2vw,16px);
  }
  .ae-gallery figure{margin:0;overflow:hidden;border-radius:6px;}
  .ae-gallery img{
    display:block;width:100%;height:100%;
    aspect-ratio:3/4;object-fit:cover;
    transition:transform .5s ease;
  }
  .ae-gallery figure:hover img{transform:scale(1.05);}

  /* ---------- DRESS CODE ---------- */
  .ae-dress{text-align:center;}
  .ae-swatches{
    display:flex;justify-content:center;gap:clamp(12px,3vw,20px);
    flex-wrap:wrap;margin:8px 0 16px;
  }
  .ae-swatch{
    width:clamp(54px,13vw,76px);
    height:clamp(54px,13vw,76px);
    border-radius:50%;
    box-shadow:0 8px 22px rgba(0,0,0,0.18);
    border:2px solid rgba(255,255,255,0.7);
  }
  .ae-dress-name{
    font-family:'Playfair Display',serif;
    font-size:clamp(1.3rem,4vw,1.9rem);
    color:var(--gold-pale);margin:0;
  }

  .ae-story{
    max-width:660px;margin:0 auto;text-align:center;
    font-size:0.97rem;line-height:1.95;color:var(--muted);font-weight:300;
  }
  .ae-story p{margin:0 0 20px;}
  .ae-story p:last-child{margin-bottom:0;}
  .ae-story-open{
    font-family:'Playfair Display',serif;font-style:italic;
    font-size:clamp(1.1rem,3vw,1.35rem);color:var(--teal-deep);
  }
  .ae-story-date{
    font-family:'Playfair Display',serif;
    font-size:clamp(1.05rem,2.8vw,1.25rem);color:var(--ink);
  }
  .ae-story-quote{
    font-family:'Great Vibes',cursive;
    font-size:clamp(1.6rem,4.6vw,2.4rem);
    color:var(--gold);line-height:1.35;
    padding:8px 0;
  }
  .ae-event-note{
    font-size:0.8rem;line-height:1.6;color:var(--muted);
    border-top:1px solid var(--line);
    margin-top:14px;padding-top:12px;font-style:italic;
  }
  .ae-dress-groups{
    display:flex;justify-content:center;flex-wrap:wrap;
    gap:clamp(30px,7vw,72px);margin-top:10px;
  }
  .ae-dress-group h3{
    font-family:'Playfair Display',serif;
    font-size:clamp(1.05rem,2.8vw,1.3rem);
    color:var(--gold-light);margin:0 0 16px;font-weight:600;
    letter-spacing:0.04em;
  }

  /* ---------- ASO EBI ---------- */
  .ae-asoebi-grid{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:clamp(18px,3vw,28px);margin-bottom:clamp(24px,4vw,36px);
  }
  .ae-asoebi-card{
    background:var(--white);border:1px solid var(--line);border-radius:8px;
    padding:clamp(22px,4vw,30px);
    box-shadow:0 10px 34px rgba(15,122,130,0.07);
  }
  .ae-asoebi-card h3{
    font-family:'Playfair Display',serif;
    font-size:clamp(1.1rem,3vw,1.35rem);
    color:var(--teal-deep);margin:0 0 16px;font-weight:600;text-align:center;
  }
  .ae-price-list{list-style:none;margin:0;padding:0;}
  .ae-price-list li{
    display:flex;justify-content:space-between;align-items:baseline;gap:14px;
    padding:11px 0;border-bottom:1px dashed rgba(200,149,43,0.28);
    font-size:0.9rem;color:var(--muted);font-weight:300;
  }
  .ae-price-list li:last-child{border-bottom:none;}
  .ae-price{
    font-family:'Playfair Display',serif;
    font-size:1.02rem;color:var(--teal-deep);font-weight:600;white-space:nowrap;
  }
  .ae-payment{
    max-width:520px;margin:0 auto;
    background:linear-gradient(150deg,var(--teal-dark) 0%,var(--teal-deep) 100%);
    border-radius:8px;padding:clamp(24px,4vw,32px);text-align:center;
    box-shadow:0 14px 40px rgba(10,86,92,0.2);
  }
  .ae-payment h3{
    font-family:'Great Vibes',cursive;font-weight:400;
    font-size:clamp(1.5rem,4vw,2rem);color:var(--gold-pale);margin:0 0 18px;
  }
  .ae-payment-rows{margin:0 0 18px;}
  .ae-payment-rows div{
    display:flex;justify-content:space-between;align-items:baseline;gap:14px;
    padding:10px 0;border-bottom:1px solid rgba(242,227,192,0.18);
  }
  .ae-payment-rows div:last-child{border-bottom:none;}
  .ae-payment-rows dt{
    font-size:0.63rem;letter-spacing:0.18em;text-transform:uppercase;
    color:rgba(242,227,192,0.75);margin:0;
  }
  .ae-payment-rows dd{
    margin:0;font-size:0.95rem;color:var(--white);font-weight:500;
    text-align:right;overflow-wrap:anywhere;
  }
  .ae-payment-note{
    font-size:0.84rem;line-height:1.7;color:rgba(247,244,238,0.86);margin:0 0 10px;
  }
  .ae-payment-note a{color:var(--gold-light);text-decoration:none;font-weight:500;white-space:nowrap;}
  .ae-payment-note a:hover{text-decoration:underline;}
  .ae-payment-warn{
    font-size:0.76rem;line-height:1.6;margin:0;
    color:var(--gold-light);font-style:italic;
    border-top:1px solid rgba(242,227,192,0.2);padding-top:12px;
  }

  .ae-form-note{
    font-size:0.8rem;line-height:1.65;color:var(--muted);
    margin:0 0 14px;font-style:italic;
  }
  .ae-form-note a{color:var(--teal);font-weight:500;white-space:nowrap;}

  /* ---------- FORM ---------- */
  .ae-card{
    max-width:660px;margin:0 auto;
    background:var(--white);
    border:1px solid var(--line);
    border-radius:8px;
    padding:clamp(24px,5vw,46px);
    box-shadow:0 18px 50px rgba(15,122,130,0.1);
  }
  .ae-card-heading{
    font-family:'Playfair Display',serif;
    font-size:clamp(1.3rem,4vw,1.75rem);
    font-weight:600;text-align:center;color:var(--teal-deep);margin:0 0 6px;
  }
  .ae-card-sub{
    display:block;text-align:center;
    font-size:0.68rem;letter-spacing:0.18em;text-transform:uppercase;
    color:var(--muted);margin-bottom:clamp(22px,4vw,32px);
  }
  .ae-field{margin-bottom:20px;}
  .ae-field label{
    display:block;font-size:0.68rem;letter-spacing:0.14em;text-transform:uppercase;
    font-weight:600;color:var(--teal-deep);margin-bottom:8px;
  }
  .ae-required{color:#b3452f;}
  .ae-field input[type=text],
  .ae-field input[type=tel],
  .ae-field input[type=email],
  .ae-field select,
  .ae-field textarea{
    width:100%;padding:13px 14px;
    border:1px solid #d9dfe0;border-radius:4px;
    font-family:'Montserrat',sans-serif;font-size:0.92rem;
    color:var(--ink);background:var(--white);
    transition:border-color .2s,box-shadow .2s;
  }
  .ae-field textarea{min-height:96px;resize:vertical;}
  .ae-field input:focus,.ae-field select:focus,.ae-field textarea:focus{
    outline:none;border-color:var(--teal);
    box-shadow:0 0 0 3px rgba(15,122,130,0.13);
  }
  .ae-fieldset{
    border:1px solid var(--line);border-radius:4px;
    padding:18px 18px 6px;margin:0 0 20px;
  }
  .ae-fieldset legend{
    font-size:0.68rem;letter-spacing:0.14em;text-transform:uppercase;
    font-weight:600;color:var(--teal-deep);padding:0 8px;
  }
  .ae-radio-group{display:flex;gap:20px;flex-wrap:wrap;margin-bottom:14px;}
  .ae-radio-group label{
    display:flex;align-items:center;gap:8px;
    font-size:0.9rem;letter-spacing:0;text-transform:none;font-weight:400;
    color:var(--ink);cursor:pointer;margin:0;
  }
  .ae-radio-group input[type=radio]{accent-color:var(--teal);width:16px;height:16px;}
  .ae-submit{
    width:100%;padding:15px 20px;border:none;border-radius:4px;
    background:linear-gradient(135deg,var(--teal-deep) 0%,var(--teal) 100%);
    color:var(--gold-pale);
    font-family:'Montserrat',sans-serif;
    font-size:0.76rem;letter-spacing:0.2em;text-transform:uppercase;font-weight:600;
    cursor:pointer;
    display:inline-flex;align-items:center;justify-content:center;gap:10px;
    transition:transform .15s,box-shadow .25s,opacity .2s;
  }
  .ae-submit:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 10px 26px rgba(10,86,92,0.3);}
  .ae-submit:disabled{opacity:0.75;cursor:not-allowed;}
  .ae-spinner{
    display:none;width:14px;height:14px;
    border:2px solid rgba(242,227,192,0.35);
    border-top-color:var(--gold-pale);
    border-radius:50%;
    animation:ae-spin .6s linear infinite;
  }
  .ae-submit.is-loading .ae-spinner{display:inline-block;}
  @keyframes ae-spin{to{transform:rotate(360deg);}}
  @media (prefers-reduced-motion:reduce){.ae-spinner{animation-duration:1.6s;}}

  .ae-form-message{margin:0 0 16px;font-size:0.86rem;text-align:center;min-height:1px;}
  .ae-form-message--error{color:#b3452f;}
  .ae-error-text{
    display:block;margin-top:6px;font-size:0.76rem;color:#b3452f;
    letter-spacing:0;text-transform:none;font-weight:400;
  }
  .ae-confirmation{
    text-align:center;
    padding:clamp(26px,5vw,42px) clamp(18px,4vw,30px);
    background:linear-gradient(150deg,var(--teal-dark) 0%,var(--teal) 100%);
    border-radius:8px;color:var(--white);
  }
  .ae-confirmation h4{
    font-family:'Great Vibes',cursive;
    font-size:clamp(1.8rem,5.5vw,2.5rem);
    color:var(--gold-pale);margin:0 0 14px;font-weight:400;
  }
  .ae-confirmation p{
    font-family:'Playfair Display',serif;
    font-size:clamp(1rem,2.8vw,1.15rem);
    line-height:1.7;margin:0;color:rgba(255,255,255,0.94);
  }

  /* ---------- FOOTER ---------- */
  .ae-footer{
    background:var(--teal-dark);
    color:rgba(247,244,238,0.62);
    text-align:center;
    padding:clamp(32px,5vw,48px) 20px;
  }
  .ae-footer-names{
    font-family:'Great Vibes',cursive;
    font-size:clamp(1.6rem,4.5vw,2.2rem);
    color:var(--gold-pale);margin:0 0 8px;
  }
  .ae-footer p{margin:0;font-size:0.7rem;letter-spacing:0.16em;text-transform:uppercase;}

  /* ---------- FIXED RSVP ---------- */
  .ae-fab{
    position:fixed;left:50%;bottom:clamp(16px,3.5vw,26px);
    transform:translateX(-50%);z-index:45;
    display:inline-flex;align-items:center;gap:9px;
    padding:13px clamp(24px,5vw,34px);
    border-radius:999px;
    background:linear-gradient(135deg,var(--gold) 0%,var(--gold-light) 100%);
    color:var(--teal-dark);
    font-size:0.7rem;letter-spacing:0.22em;text-transform:uppercase;font-weight:600;
    text-decoration:none;white-space:nowrap;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
    transition:opacity .3s,visibility .3s,transform .2s;
  }
  .ae-fab:hover{transform:translateX(-50%) translateY(-2px);}
  .ae-fab.is-hidden{opacity:0;visibility:hidden;pointer-events:none;}
  @media (max-width:400px){
    .ae-fab{font-size:0.64rem;letter-spacing:0.16em;padding:12px 22px;}
  }
</style>
</head>
<body>

<nav class="ae-nav">
  <a href="#top" class="ae-nav-brand">Ayodele &amp; Elochukwu</a>
  <button type="button" class="ae-menu-toggle" id="ae-toggle" aria-label="Open menu">Menu</button>
</nav>

<div class="ae-overlay" id="ae-overlay"></div>
<aside class="ae-drawer" id="ae-drawer" aria-hidden="true">
  <div class="ae-drawer-head">
    <p class="ae-drawer-title">Menu</p>
    <button type="button" class="ae-drawer-cancel" id="ae-drawer-cancel" aria-label="Close menu">&times;</button>
  </div>
  <a href="#top">Home</a>
  <a href="#story">Our Story</a>
  <a href="#events">Events</a>
  <a href="#gallery">Gallery</a>
  <a href="#dresscode">Dress Code</a>
  <a href="#asoebi">Aso Ebi</a>
  <a href="#rsvp">RSVP</a>
</aside>

<header class="ae-hero" id="top">
  <div class="ae-slides" id="ae-slides">
    <div class="ae-slide is-active" style="background-image:url('<?= AE_IMG_BASE ?>8.jpeg');"></div>
    <div class="ae-slide" style="background-image:url('<?= AE_IMG_BASE ?>4.jpeg');"></div>
    <div class="ae-slide" style="background-image:url('<?= AE_IMG_BASE ?>2.jpeg');"></div>
    <div class="ae-slide" style="background-image:url('<?= AE_IMG_BASE ?>7.jpeg');"></div>
    <div class="ae-slide" style="background-image:url('<?= AE_IMG_BASE ?>5.jpeg');"></div>
  </div>

  <div class="ae-hero-inner ae-wrap">
    <img class="ae-logo" src="assets/logo-circle.png" alt="Ayodele and Elochukwu monogram" width="900" height="900">
    <p class="ae-announce">We&rsquo;re Getting Married</p>
    <h1 class="ae-names">Ayodele &amp; Elochukwu</h1>
    <p class="ae-date">21 &middot; 11 &middot; 2026</p>

    <div class="ae-countdown" id="ae-countdown">
      <div class="ae-count-box"><span class="ae-count-num" id="ae-days">00</span><span class="ae-count-lbl">Days</span></div>
      <div class="ae-count-box"><span class="ae-count-num" id="ae-hours">00</span><span class="ae-count-lbl">Hours</span></div>
      <div class="ae-count-box"><span class="ae-count-num" id="ae-minutes">00</span><span class="ae-count-lbl">Minutes</span></div>
      <div class="ae-count-box"><span class="ae-count-num" id="ae-seconds">00</span><span class="ae-count-lbl">Seconds</span></div>
    </div>

    <a href="#rsvp" class="ae-hero-cta">RSVP Now</a>
  </div>
</header>

<section class="ae-section" id="story">
  <div class="ae-wrap">
    <div class="ae-section-head">
      <p class="ae-eyebrow">Our Journey</p>
      <h2 class="ae-title">Our Love Story</h2>
    </div>

    <div class="ae-story">
      <p class="ae-story-open">Some of life&rsquo;s best gifts don&rsquo;t come wrapped.</p>
      <p class="ae-story-date">Ours came on Christmas Day, 2022.</p>
      <p>
        What began as a simple meeting grew into laughter, friendship, love, and a
        beautiful life together. Looking back, we now know that the greatest gift of that
        Christmas wasn&rsquo;t under the tree &mdash; it was finding each other.
      </p>
      <p class="ae-story-quote">My Christmas Present &rsquo;22, now, my forever.</p>
      <p>
        As we begin our next chapter, we would love for you to join us in celebrating
        our love.
      </p>
    </div>
  </div>
</section>

<section class="ae-section ae-section--tint" id="events">
  <div class="ae-wrap">
    <div class="ae-section-head">
      <p class="ae-eyebrow">Save The Date</p>
      <h2 class="ae-title">Wedding Events</h2>
    </div>
    <div class="ae-events">
      <div class="ae-event">
        <h3>Traditional Wedding</h3>
        <p class="ae-event-sub">Ceremony</p>
        <p><strong>Saturday, 21st November 2026</strong></p>
        <p><strong>2:00 PM</strong> prompt</p>
        <p>Monarch Event Centre</p>
        <p class="ae-event-note">
          Kindly arrive on time so seating can be organised smoothly.
        </p>
      </div>
      <div class="ae-event">
        <h3>Reception</h3>
        <p class="ae-event-sub">&amp; After Party</p>
        <p><strong>Immediately after</strong></p>
        <p>the traditional ceremony</p>
        <p class="ae-event-note">
          Same venue &mdash; stay with us and celebrate into the night.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="ae-section" id="gallery">
  <div class="ae-wrap">
    <div class="ae-section-head">
      <p class="ae-eyebrow">Moments</p>
      <h2 class="ae-title">Our Gallery</h2>
    </div>
    <div class="ae-gallery">
      <?php foreach ([1, 2, 3, 4, 5, 6, 7, 8] as $n): ?>
        <figure>
          <img src="<?= AE_IMG_BASE . $n ?>.jpeg" alt="Ayodele and Elochukwu, photo <?= $n ?>" loading="lazy">
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="ae-section ae-section--teal" id="dresscode">
  <div class="ae-wrap ae-dress">
    <div class="ae-section-head">
      <p class="ae-eyebrow">Colours Of The Day</p>
      <h2 class="ae-title">Dress Code</h2>
      <p class="ae-lead">We would love to see you in our colours as we celebrate.</p>
    </div>
    <div class="ae-dress-groups">
      <div class="ae-dress-group">
        <h3>Ladies</h3>
        <div class="ae-swatches">
          <span class="ae-swatch" style="background:#40e0d0;" title="Turquoise"></span>
        </div>
        <p class="ae-dress-name">Turquoise</p>
      </div>
      <div class="ae-dress-group">
        <h3>Gentlemen</h3>
        <div class="ae-swatches">
          <span class="ae-swatch" style="background:#b0d4e8;" title="Powder Blue"></span>
        </div>
        <p class="ae-dress-name">Powder Blue</p>
      </div>
    </div>
  </div>
</section>

<section class="ae-section" id="asoebi">
  <div class="ae-wrap">
    <div class="ae-section-head">
      <p class="ae-eyebrow">Aso Ebi</p>
      <h2 class="ae-title">Fabric &amp; Pricing</h2>
      <p class="ae-lead">
        Aso Ebi is available for purchase. Please call to confirm fabric is still
        available <strong>before</strong> making any payment.
      </p>
    </div>

    <div class="ae-asoebi-grid">
      <div class="ae-asoebi-card">
        <h3>Ladies</h3>
        <ul class="ae-price-list">
          <li><span>Gele</span><span class="ae-price">&#8358;20,000</span></li>
        </ul>
      </div>

      <div class="ae-asoebi-card">
        <h3>Gentlemen</h3>
        <ul class="ae-price-list">
          <li><span>10 yards with fila</span><span class="ae-price">&#8358;150,000</span></li>
          <li><span>5 yards with fila</span><span class="ae-price">&#8358;80,000</span></li>
          <li><span>Fila only</span><span class="ae-price">&#8358;10,000</span></li>
        </ul>
      </div>
    </div>

    <div class="ae-payment">
      <h3>Payment Details</h3>
      <dl class="ae-payment-rows">
        <div><dt>Account Name</dt><dd>Okeoghene A. Ekere</dd></div>
        <div><dt>Bank</dt><dd>Stanbic IBTC</dd></div>
        <div><dt>Account Number</dt><dd>0023138734</dd></div>
      </dl>
      <p class="ae-payment-note">
        Kindly forward proof of payment to
        <a href="tel:07035161687">070 3516 1687</a> &amp;
        <a href="tel:08142025400">081 4202 5400</a>
      </p>
      <p class="ae-payment-warn">
        Please confirm availability on the numbers above before paying.
      </p>
    </div>
  </div>
</section>

<section class="ae-section ae-section--tint" id="rsvp">
  <div class="ae-section-head">
    <p class="ae-eyebrow">Kindly Respond</p>
    <h2 class="ae-title">Be Our Guest</h2>
  </div>

  <div class="ae-card">
    <div class="ae-confirmation" id="ae-confirmation" <?= $submitted ? '' : 'hidden' ?>>
      <h4>Thank You!</h4>
      <p>Your RSVP has been received.<br>We can&rsquo;t wait to celebrate with you.</p>
    </div>

    <div <?= $submitted ? 'hidden' : '' ?> id="ae-form-shell">
      <h3 class="ae-card-heading">Confirm Your Attendance</h3>
      <span class="ae-card-sub">Ayodele &amp; Elochukwu &bull; 21 November 2026</span>

      <p class="ae-form-message <?= $failed ? 'ae-form-message--error' : '' ?>" id="ae-form-message"><?= $failed ? 'Something went wrong. Please check your details and try again.' : '' ?></p>

      <form class="ae-rsvp-form" id="ae-rsvp-form" action="<?= htmlspecialchars($actionUrl, ENT_QUOTES) ?>" method="post" novalidate>
        <input type="hidden" name="form_slug" value="<?= htmlspecialchars(PWS_FORM_SLUG, ENT_QUOTES) ?>">

        <div class="ae-field">
          <label for="title">Title</label>
          <select id="title" name="title">
            <option value="">-</option>
            <option>Mr</option><option>Mrs</option><option>Miss</option>
            <option>Ms</option><option>Dr</option><option>Chief</option>
            <option>Engr</option><option>Prof</option><option>Rev</option>
            <option>Pastor</option><option>Barr</option>
          </select>
        </div>

        <div class="ae-field">
          <label for="full_name">Full Name <span class="ae-required">*</span></label>
          <input type="text" id="full_name" name="full_name" required>
        </div>

        <div class="ae-field">
          <label for="phone">Phone Number <span class="ae-required">*</span></label>
          <input type="tel" id="phone" name="phone" required>
        </div>

        <div class="ae-field">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email">
        </div>

        <fieldset class="ae-fieldset">
          <legend>Will you be attending? <span class="ae-required">*</span></legend>
          <div class="ae-radio-group">
            <label><input type="radio" name="attending" value="yes" required> Yes</label>
            <label><input type="radio" name="attending" value="no"> No</label>
          </div>
        </fieldset>

        <div class="ae-field" hidden data-toggle="attending:yes">
          <label for="guest_count">Number Attending</label>
          <select id="guest_count" name="guest_count">
            <option value="">-</option>
            <option value="just_me">Just me</option>
            <option value="plus_one">Me plus one</option>
            <option value="family">Family</option>
          </select>
        </div>

        <div class="ae-field" hidden data-toggle="guest_count:plus_one,family">
          <label for="guest_names">Guest Name(s)</label>
          <input type="text" id="guest_names" name="guest_names" placeholder="Full name(s) of your guest(s)">
        </div>

        <div class="ae-field">
          <label for="rsvp_category">Whose Guest Are You?</label>
          <select id="rsvp_category" name="rsvp_category">
            <option value="">-</option>
            <option value="Bride's Family">Bride&rsquo;s Family</option>
            <option value="Groom's Family">Groom&rsquo;s Family</option>
            <option value="Bride's Friend">Bride&rsquo;s Friend</option>
            <option value="Groom's Friend">Groom&rsquo;s Friend</option>
            <option value="Colleague">Colleague</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <fieldset class="ae-fieldset" hidden data-toggle="attending:yes">
          <legend>Would you like to purchase Aso Ebi?</legend>
          <div class="ae-radio-group">
            <label><input type="radio" name="asoebi_purchase" value="yes"> Yes</label>
            <label><input type="radio" name="asoebi_purchase" value="no"> No</label>
          </div>

          <div class="ae-field" hidden data-toggle="asoebi_purchase:yes">
            <label for="asoebi_item">Item</label>
            <select id="asoebi_item" name="asoebi_item">
              <option value="">-</option>
              <option value="Gele - N20,000">Ladies &mdash; Gele (&#8358;20,000)</option>
              <option value="10 yards with fila - N150,000">Gentlemen &mdash; 10 yards with fila (&#8358;150,000)</option>
              <option value="5 yards with fila - N80,000">Gentlemen &mdash; 5 yards with fila (&#8358;80,000)</option>
              <option value="Fila only - N10,000">Gentlemen &mdash; Fila only (&#8358;10,000)</option>
            </select>
          </div>

          <div class="ae-field" hidden data-toggle="asoebi_purchase:yes">
            <label for="asoebi_qty">Quantity</label>
            <input type="number" id="asoebi_qty" name="asoebi_qty" min="1" value="1">
          </div>

          <p class="ae-form-note" hidden data-toggle="asoebi_purchase:yes">
            Please call <a href="tel:07035161687">070 3516 1687</a> to confirm fabric is
            still available before you pay. Payment details are in the Aso Ebi section above.
          </p>
        </fieldset>

        <div class="ae-field" hidden data-toggle="attending:no">
          <label for="message">Leave A Message For The Couple</label>
          <textarea id="message" name="message" placeholder="Your good wishes for Ayodele &amp; Elochukwu..."></textarea>
        </div>

        <button type="submit" class="ae-submit" id="ae-submit">
          <span>Submit</span>
          <span class="ae-spinner" aria-hidden="true"></span>
        </button>
      </form>
    </div>
  </div>
</section>

<a href="#rsvp" class="ae-fab" id="ae-fab">RSVP</a>

<footer class="ae-footer">
  <p class="ae-footer-names">Ayodele &amp; Elochukwu</p>
  <p>21 November 2026</p>
</footer>

<script>
(function(){
  // ---------- DRAWER ----------
  const toggle = document.getElementById('ae-toggle');
  const drawer = document.getElementById('ae-drawer');
  const overlay = document.getElementById('ae-overlay');
  const cancel = document.getElementById('ae-drawer-cancel');

  function openDrawer(){
    drawer.classList.add('is-open');
    overlay.classList.add('is-open');
    drawer.removeAttribute('aria-hidden');
  }
  function closeDrawer(){
    drawer.classList.remove('is-open');
    overlay.classList.remove('is-open');
    drawer.setAttribute('aria-hidden','true');
  }
  toggle.addEventListener('click', openDrawer);
  overlay.addEventListener('click', closeDrawer);
  cancel.addEventListener('click', closeDrawer);
  drawer.addEventListener('click', function(e){ if (e.target.tagName === 'A') closeDrawer(); });

  // ---------- HERO SLIDER ----------
  const slides = document.querySelectorAll('.ae-slide');
  let idx = 0;
  if (slides.length > 1){
    setInterval(function(){
      slides[idx].classList.remove('is-active');
      idx = (idx + 1) % slides.length;
      slides[idx].classList.add('is-active');
    }, 5500);
  }

  // ---------- COUNTDOWN ----------
  const weddingDate = new Date('2026-11-21T00:00:00').getTime();
  const daysEl = document.getElementById('ae-days');
  const hoursEl = document.getElementById('ae-hours');
  const minutesEl = document.getElementById('ae-minutes');
  const secondsEl = document.getElementById('ae-seconds');
  const countdownEl = document.getElementById('ae-countdown');

  function updateCountdown(){
    const diff = weddingDate - Date.now();
    if (diff <= 0){
      countdownEl.innerHTML = '<span style="font-family:\'Great Vibes\',cursive;font-size:2rem;color:#f2e3c0;">Happily Married!</span>';
      return;
    }
    daysEl.textContent    = String(Math.floor(diff / 86400000)).padStart(2,'0');
    hoursEl.textContent   = String(Math.floor((diff % 86400000) / 3600000)).padStart(2,'0');
    minutesEl.textContent = String(Math.floor((diff % 3600000) / 60000)).padStart(2,'0');
    secondsEl.textContent = String(Math.floor((diff % 60000) / 1000)).padStart(2,'0');
  }
  updateCountdown();
  setInterval(updateCountdown, 1000);

  // ---------- FORM ----------
  const form = document.getElementById('ae-rsvp-form');
  const formShell = document.getElementById('ae-form-shell');
  const confirmationEl = document.getElementById('ae-confirmation');
  const messageEl = document.getElementById('ae-form-message');
  const fab = document.getElementById('ae-fab');
  const rsvpSection = document.getElementById('rsvp');

  function setFabHidden(hidden){ fab.classList.toggle('is-hidden', hidden); }

  if (confirmationEl && !confirmationEl.hidden){
    setFabHidden(true);
  } else if ('IntersectionObserver' in window){
    new IntersectionObserver(function(entries){
      entries.forEach(function(entry){ setFabHidden(entry.isIntersecting); });
    }, { rootMargin: '-15% 0px -15% 0px' }).observe(rsvpSection);
  }

  function getFieldValue(name){
    const radios = form.querySelectorAll('input[type="radio"][name="' + name + '"]');
    if (radios.length){
      const checked = form.querySelector('input[name="' + name + '"]:checked');
      return checked ? checked.value : null;
    }
    const field = form.querySelector('[name="' + name + '"]');
    return field ? field.value : null;
  }

  function applyToggle(el){
    const parts = el.getAttribute('data-toggle').split(':');
    const values = parts[1].split(',');
    const value = getFieldValue(parts[0]);
    // A nested block only shows when every ancestor block is showing too --
    // otherwise the inner toggle would re-enable fields inside a hidden parent
    // and submit them.
    const parentHidden = el.parentElement && el.parentElement.closest('[data-toggle][hidden]');
    const show = !parentHidden && value !== null && values.indexOf(value) !== -1;
    el.hidden = !show;
    el.querySelectorAll('input, select, textarea').forEach(function(field){
      field.disabled = !show;
    });
  }

  form.querySelectorAll('[data-toggle]').forEach(function(el){
    const fieldName = el.getAttribute('data-toggle').split(':')[0];
    form.querySelectorAll('[name="' + fieldName + '"]').forEach(function(control){
      control.addEventListener('change', function(){
        form.querySelectorAll('[data-toggle]').forEach(applyToggle);
      });
    });
    applyToggle(el);
  });

  form.addEventListener('submit', function(e){
    e.preventDefault();
    form.querySelectorAll('.ae-error-text').forEach(function(el){ el.remove(); });
    messageEl.className = 'ae-form-message';
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
          messageEl.className = 'ae-form-message ae-form-message--error';
          messageEl.textContent = 'Please fix the highlighted fields and try again.';
          const errors = (data && data.errors) || {};
          Object.keys(errors).forEach(function(field){
            const input = form.querySelector('[name="' + field + '"]');
            const text = document.createElement('span');
            text.className = 'ae-error-text';
            text.textContent = errors[field];
            if (input && input.closest('.ae-field')){
              input.closest('.ae-field').appendChild(text);
            } else if (input && input.closest('.ae-fieldset')){
              input.closest('.ae-fieldset').appendChild(text);
            }
          });
        }
      })
      .catch(function(){
        messageEl.className = 'ae-form-message ae-form-message--error';
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
