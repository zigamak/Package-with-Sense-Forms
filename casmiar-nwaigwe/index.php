<?php
declare(strict_types=1);
require_once __DIR__ . '/../rsvp-core/config.php';

const PWS_FORM_SLUG = 'casmiar-nwaigwe';

$submitted = ($_GET['submitted'] ?? '') === '1';
$failed    = ($_GET['submitted'] ?? '') === '0';
$actionUrl = PWS_SITE_URL . '/rsvp-core/submit.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dr. Casmiar Nwaigwe&rsquo;s Birthday Celebration &mdash; RSVP</title>
  <meta name="theme-color" content="#0a1a3f">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --navy:#0a1a3f;
    --navy-deep:#050f26;
    --royal-blue:#13245c;
    --red:#8e1b2e;
    --red-deep:#6c1220;
    --gold:#c9a24a;
    --gold-light:#e8cd85;
    --gold-pale:#f5e9c8;
    --white:#fff;
    --ivory:#faf7ee;
    --ink:#241c10;
    --muted:#6b6455;
    --line:rgba(201,162,74,0.35);
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    background:var(--ivory);
    color:var(--ink);
    font-family:'Montserrat',sans-serif;
    -webkit-font-smoothing:antialiased;
  }
  .cn-wrap{max-width:1100px;margin:0 auto;}

  /* ---------- NAV ---------- */
  .cn-nav{
    position:fixed;top:0;left:0;right:0;z-index:50;
    display:flex;flex-direction:column;
    background:rgba(5,15,38,0.92);
    backdrop-filter:blur(8px);
  }
  .cn-nav-row{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px clamp(16px,4vw,32px);
  }
  .cn-nav-kente{display:flex;width:100%;height:5px;overflow:hidden;}
  .cn-nav-kente span{flex:1 0 auto;width:26px;height:5px;}
  .cn-nav-kente span:nth-child(4n+1){background:var(--red);}
  .cn-nav-kente span:nth-child(4n+2){background:var(--gold);}
  .cn-nav-kente span:nth-child(4n+3){background:var(--navy);}
  .cn-nav-kente span:nth-child(4n){background:var(--gold-light);}
  .cn-nav-brand{
    display:flex;align-items:center;gap:10px;
    font-family:'Cinzel',serif;font-weight:600;
    font-size:clamp(0.95rem,2.6vw,1.25rem);
    color:var(--gold-pale);text-decoration:none;white-space:nowrap;
  }
  .cn-nav-crown{width:24px;height:24px;flex:none;}
  .cn-menu-toggle{
    background:none;border:1px solid rgba(245,233,200,0.5);border-radius:3px;
    color:var(--gold-pale);font-family:'Montserrat',sans-serif;
    font-size:0.66rem;letter-spacing:0.18em;text-transform:uppercase;
    padding:9px 16px;cursor:pointer;transition:background .2s,color .2s;
  }
  .cn-menu-toggle:hover{background:var(--gold);border-color:var(--gold);color:var(--navy-deep);}
  .cn-overlay{
    position:fixed;inset:0;background:rgba(5,15,38,0.6);
    opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s;z-index:55;
  }
  .cn-overlay.is-open{opacity:1;visibility:visible;}
  .cn-drawer{
    position:fixed;top:0;right:0;bottom:0;width:min(320px,86vw);
    background:var(--navy-deep);transform:translateX(100%);
    transition:transform .32s ease;z-index:60;padding:26px 24px;overflow-y:auto;
    border-left:1px solid rgba(201,162,74,0.3);
  }
  .cn-drawer.is-open{transform:translateX(0);}
  .cn-drawer-head{
    display:flex;align-items:center;justify-content:space-between;
    border-bottom:1px solid rgba(245,233,200,0.25);padding-bottom:16px;margin-bottom:22px;
  }
  .cn-drawer-title{font-family:'Cinzel',serif;font-size:1.3rem;color:var(--gold-pale);margin:0;}
  .cn-drawer-cancel{background:none;border:none;color:var(--gold-pale);font-size:1.6rem;line-height:1;cursor:pointer;padding:0 4px;}
  .cn-drawer a{
    display:block;color:var(--ivory);text-decoration:none;font-size:0.76rem;
    letter-spacing:0.16em;text-transform:uppercase;padding:13px 0;
    border-bottom:1px solid rgba(245,233,200,0.12);transition:color .2s,padding-left .2s;
  }
  .cn-drawer a:hover{color:var(--gold-light);padding-left:6px;}

  /* ---------- HERO ---------- */
  .cn-hero{
    position:relative;min-height:100svh;
    display:flex;align-items:center;justify-content:center;text-align:center;
    padding:clamp(100px,16vw,140px) 20px clamp(60px,9vw,90px);
    overflow:hidden;
    background:
      radial-gradient(ellipse at 50% 0%, rgba(142,27,46,0.35) 0%, rgba(10,26,63,0) 55%),
      linear-gradient(180deg, var(--navy-deep) 0%, var(--navy) 55%, var(--royal-blue) 100%);
  }
  .cn-hero-pattern{
    position:absolute;inset:0;z-index:0;opacity:0.16;
    background-repeat:repeat;background-size:140px 140px;
  }
  .cn-hero-vignette{
    position:absolute;inset:0;z-index:1;
    background:radial-gradient(ellipse at center, rgba(10,26,63,0) 40%, rgba(5,15,38,0.75) 100%);
  }
  .cn-hero-corner{
    position:absolute;z-index:1;width:clamp(70px,14vw,140px);height:auto;opacity:0.85;
  }
  .cn-hero-corner--tl{top:0;left:0;}
  .cn-hero-corner--tr{top:0;right:0;transform:scaleX(-1);}
  .cn-hero-corner--bl{bottom:0;left:0;transform:scaleY(-1);}
  .cn-hero-corner--br{bottom:0;right:0;transform:scale(-1,-1);}
  .cn-hero-inner{position:relative;z-index:2;width:100%;}
  .cn-crown-mark{width:clamp(46px,9vw,64px);height:auto;margin:0 auto clamp(10px,2vw,16px);display:block;}
  .cn-portrait-frame{
    position:relative;
    width:clamp(150px,30vw,220px);
    aspect-ratio:1/1;
    margin:0 auto clamp(18px,3.5vw,28px);
    border-radius:50%;
    padding:6px;
    background:linear-gradient(135deg,var(--gold) 0%,var(--gold-light) 50%,var(--gold) 100%);
    box-shadow:0 14px 40px rgba(0,0,0,0.5);
  }
  .cn-portrait-frame img{
    width:100%;height:100%;
    border-radius:50%;
    object-fit:cover;
    object-position:center 15%;
    display:block;
    border:3px solid var(--navy-deep);
  }
  .cn-announce{
    font-family:'Cinzel',serif;font-weight:600;
    font-size:clamp(0.85rem,2.6vw,1.15rem);letter-spacing:0.3em;text-transform:uppercase;
    color:var(--gold-light);margin:0 0 14px;
  }
  .cn-names{
    font-family:'Playfair Display',serif;font-style:italic;font-weight:600;
    font-size:clamp(2.2rem,7.5vw,4.4rem);
    color:var(--white);margin:0 0 6px;line-height:1.12;
    text-shadow:0 3px 20px rgba(0,0,0,0.5);
  }
  .cn-subtitle{
    font-family:'Cinzel',serif;font-weight:500;
    font-size:clamp(0.9rem,2.6vw,1.2rem);letter-spacing:0.08em;
    color:var(--gold-pale);margin:0 0 clamp(18px,3vw,26px);
  }
  .cn-theme-badge{
    display:inline-block;padding:9px 26px;margin-bottom:clamp(26px,5vw,40px);
    border:1px solid var(--gold);border-radius:999px;
    font-family:'Cinzel',serif;font-weight:600;
    font-size:clamp(0.68rem,2vw,0.82rem);letter-spacing:0.28em;text-transform:uppercase;
    color:var(--red-deep);
    background:linear-gradient(135deg,var(--gold-light) 0%,var(--gold) 100%);
  }
  .cn-date{
    font-family:'Montserrat',sans-serif;font-weight:600;
    font-size:clamp(0.78rem,2.4vw,0.95rem);letter-spacing:0.24em;text-transform:uppercase;
    color:var(--white);margin:0 0 8px;
  }
  .cn-venue{
    font-size:clamp(0.72rem,2vw,0.85rem);letter-spacing:0.1em;
    color:rgba(245,233,200,0.75);margin:0 0 clamp(28px,5vw,40px);
  }
  .cn-countdown{
    display:flex;justify-content:center;flex-wrap:wrap;gap:clamp(9px,2.6vw,18px);
    margin-bottom:clamp(28px,5vw,40px);
  }
  .cn-count-box{
    min-width:clamp(62px,17vw,86px);padding:13px 8px 10px;
    border:1px solid rgba(201,162,74,0.45);border-radius:4px;
    background:rgba(255,255,255,0.06);backdrop-filter:blur(3px);
  }
  .cn-count-num{
    display:block;font-family:'Playfair Display',serif;font-weight:700;
    font-size:clamp(1.4rem,4.6vw,2rem);color:var(--gold-light);line-height:1;
  }
  .cn-count-lbl{
    display:block;margin-top:6px;font-size:0.58rem;letter-spacing:0.2em;
    text-transform:uppercase;color:rgba(255,255,255,0.7);
  }
  .cn-hero-cta{
    display:inline-flex;align-items:center;gap:9px;
    padding:15px clamp(28px,5vw,40px);border-radius:999px;
    background:linear-gradient(135deg,var(--red) 0%,var(--red-deep) 100%);
    color:var(--gold-pale);
    font-family:'Cinzel',serif;font-weight:600;
    font-size:0.74rem;letter-spacing:0.22em;text-transform:uppercase;
    text-decoration:none;border:1px solid var(--gold);
    box-shadow:0 12px 30px rgba(0,0,0,0.4);
    transition:transform .2s,box-shadow .25s;
  }
  .cn-hero-cta:hover{transform:translateY(-2px);box-shadow:0 16px 38px rgba(0,0,0,0.5);}

  /* ---------- SECTION SHELL ---------- */
  .cn-section{padding:clamp(52px,9vw,90px) 20px;}
  .cn-section--tint{background:var(--white);}
  .cn-section--navy{
    background:linear-gradient(160deg,var(--navy-deep) 0%,var(--navy) 100%);
    color:var(--ivory);
  }
  .cn-section-head{text-align:center;margin-bottom:clamp(30px,5vw,46px);}
  .cn-eyebrow{
    font-size:0.64rem;letter-spacing:0.3em;text-transform:uppercase;
    color:var(--red);font-weight:700;margin:0 0 12px;
  }
  .cn-section--navy .cn-eyebrow{color:var(--gold-light);}
  .cn-title{
    font-family:'Cinzel',serif;font-weight:700;
    font-size:clamp(1.8rem,5.5vw,2.8rem);
    color:var(--navy);margin:0;line-height:1.2;
  }
  .cn-section--navy .cn-title{color:var(--gold-pale);}
  .cn-lead{
    max-width:640px;margin:18px auto 0;
    font-size:0.95rem;line-height:1.85;color:var(--muted);font-weight:300;text-align:center;
  }
  .cn-section--navy .cn-lead{color:rgba(250,247,238,0.82);}

  /* ---------- MOTIF DIVIDER ---------- */
  .cn-divider{display:flex;align-items:center;justify-content:center;gap:14px;margin:0 0 clamp(24px,4vw,34px);}
  .cn-divider svg{width:22px;height:22px;flex:none;}
  .cn-divider-line{height:1px;width:clamp(40px,10vw,90px);background:linear-gradient(90deg,transparent,var(--gold),transparent);}


  /* ---------- EVENT / INFO CARDS ---------- */
  .cn-events{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:clamp(18px,3vw,28px);
  }
  .cn-event{
    background:var(--white);border:1px solid var(--line);border-radius:10px;
    padding:clamp(26px,4vw,36px);text-align:center;
    box-shadow:0 12px 34px rgba(10,26,63,0.08);
    position:relative;overflow:hidden;
  }
  .cn-event::before{
    content:'';position:absolute;top:0;left:0;right:0;height:4px;
    background:linear-gradient(90deg,var(--red) 0%,var(--gold) 50%,var(--navy) 100%);
  }
  .cn-event-icon{width:34px;height:34px;margin:6px auto 14px;}
  .cn-event h3{
    font-family:'Cinzel',serif;font-weight:700;
    font-size:clamp(1.05rem,2.8vw,1.3rem);color:var(--navy);margin:0 0 10px;
  }
  .cn-event p{margin:0 0 6px;font-size:0.9rem;line-height:1.7;color:var(--muted);font-weight:400;}
  .cn-event strong{color:var(--ink);font-weight:600;}

  /* ---------- HOTEL RESERVATION ---------- */
  .cn-hotel{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:clamp(20px,3vw,30px);max-width:900px;margin:0 auto;
  }
  .cn-hotel-card{
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(201,162,74,0.4);border-radius:10px;
    padding:clamp(24px,4vw,32px);
  }
  .cn-hotel-card h3{
    font-family:'Cinzel',serif;font-weight:700;
    font-size:1.05rem;letter-spacing:0.05em;text-transform:uppercase;
    color:var(--gold-light);margin:0 0 18px;text-align:center;
  }
  .cn-rate-row{
    display:flex;align-items:baseline;justify-content:space-between;gap:10px;
    padding:12px 0;border-bottom:1px solid rgba(245,233,200,0.15);
  }
  .cn-rate-row:last-of-type{border-bottom:none;}
  .cn-rate-name{font-size:0.9rem;color:var(--ivory);font-weight:500;}
  .cn-rate-price{font-family:'Playfair Display',serif;font-size:1.15rem;color:var(--gold-light);font-weight:600;white-space:nowrap;}
  .cn-rate-note{font-size:0.66rem;color:rgba(250,247,238,0.6);display:block;margin-top:2px;}
  .cn-code{
    margin-top:16px;padding:12px 14px;border-radius:6px;
    background:rgba(201,162,74,0.12);border:1px dashed var(--gold);
    font-size:0.8rem;color:var(--gold-pale);text-align:center;line-height:1.6;
  }
  .cn-code strong{color:var(--white);}
  .cn-contact-list{list-style:none;margin:0;padding:0;}
  .cn-contact-list li{
    display:flex;align-items:center;gap:12px;padding:10px 0;
    border-bottom:1px solid rgba(245,233,200,0.15);font-size:0.88rem;color:var(--ivory);
  }
  .cn-contact-list li:last-child{border-bottom:none;}
  .cn-contact-list svg{width:18px;height:18px;flex:none;color:var(--gold-light);}
  .cn-contact-list a{color:var(--gold-pale);text-decoration:none;}
  .cn-contact-list a:hover{text-decoration:underline;}
  .cn-hotel-note{
    max-width:700px;margin:clamp(26px,4vw,36px) auto 0;text-align:center;
    font-size:0.86rem;line-height:1.8;color:rgba(250,247,238,0.78);font-style:italic;
  }

  /* ---------- NOTICE ---------- */
  .cn-notice{
    background:linear-gradient(150deg,var(--red-deep) 0%,var(--red) 55%,var(--red-deep) 100%);
    color:var(--white);text-align:center;
    padding:clamp(46px,8vw,80px) 20px;
    border-top:1px solid rgba(201,162,74,0.4);
    border-bottom:1px solid rgba(201,162,74,0.4);
  }
  .cn-notice-kicker{
    font-family:'Cinzel',serif;font-weight:600;
    font-size:clamp(0.62rem,2vw,0.74rem);letter-spacing:0.32em;text-transform:uppercase;
    color:var(--gold-light);margin:0 0 clamp(12px,2.5vw,18px);
  }
  .cn-notice-main{
    font-family:'Playfair Display',serif;font-style:italic;font-weight:700;
    font-size:clamp(1.8rem,7vw,3.4rem);line-height:1.15;margin:0;
    color:var(--gold-pale);
  }
  .cn-notice-rule{
    width:clamp(70px,16vw,140px);height:1px;
    background:linear-gradient(90deg,transparent,var(--gold),transparent);
    margin:clamp(26px,5vw,40px) auto;
  }
  .cn-notice-date{
    font-family:'Cinzel',serif;font-weight:700;
    font-size:clamp(1.5rem,5.5vw,2.6rem);line-height:1.2;margin:0;color:var(--white);
  }

  /* ---------- FORM ---------- */
  .cn-card{
    max-width:660px;margin:0 auto;background:var(--white);
    border:1px solid var(--line);border-radius:10px;
    padding:clamp(24px,5vw,46px);
    box-shadow:0 18px 50px rgba(10,26,63,0.12);
  }
  .cn-card-heading{
    font-family:'Cinzel',serif;font-weight:700;
    font-size:clamp(1.2rem,3.6vw,1.6rem);text-align:center;color:var(--navy);margin:0 0 6px;
  }
  .cn-card-sub{
    display:block;text-align:center;
    font-size:0.68rem;letter-spacing:0.18em;text-transform:uppercase;
    color:var(--muted);margin-bottom:clamp(22px,4vw,32px);
  }
  .cn-field{margin-bottom:20px;}
  .cn-field label{
    display:block;font-size:0.68rem;letter-spacing:0.14em;text-transform:uppercase;
    font-weight:700;color:var(--navy);margin-bottom:8px;
  }
  .cn-required{color:var(--red);}
  .cn-field input[type=text],
  .cn-field input[type=tel],
  .cn-field input[type=email],
  .cn-field select,
  .cn-field textarea{
    width:100%;padding:13px 14px;border:1px solid #ddd6c4;border-radius:5px;
    font-family:'Montserrat',sans-serif;font-size:0.92rem;color:var(--ink);background:var(--white);
    transition:border-color .2s,box-shadow .2s;
  }
  .cn-field textarea{min-height:96px;resize:vertical;}
  .cn-field input:focus,.cn-field select:focus,.cn-field textarea:focus{
    outline:none;border-color:var(--gold);
    box-shadow:0 0 0 3px rgba(201,162,74,0.2);
  }
  .cn-fieldset{border:1px solid var(--line);border-radius:5px;padding:18px 18px 6px;margin:0 0 20px;}
  .cn-fieldset legend{
    font-size:0.68rem;letter-spacing:0.14em;text-transform:uppercase;
    font-weight:700;color:var(--navy);padding:0 8px;
  }
  .cn-radio-group{display:flex;gap:20px;flex-wrap:wrap;margin-bottom:14px;}
  .cn-radio-group label{
    display:flex;align-items:center;gap:8px;font-size:0.9rem;font-weight:400;
    letter-spacing:0;text-transform:none;color:var(--ink);cursor:pointer;margin:0;
  }
  .cn-radio-group input[type=radio]{accent-color:var(--red);width:16px;height:16px;}
  .cn-submit{
    width:100%;padding:15px 20px;border:none;border-radius:5px;
    background:linear-gradient(135deg,var(--navy) 0%,var(--royal-blue) 100%);
    color:var(--gold-pale);
    font-family:'Cinzel',serif;font-weight:600;
    font-size:0.76rem;letter-spacing:0.2em;text-transform:uppercase;
    cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:10px;
    transition:transform .15s,box-shadow .25s,opacity .2s;
    border:1px solid var(--gold);
  }
  .cn-submit:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 10px 26px rgba(10,26,63,0.35);}
  .cn-submit:disabled{opacity:0.75;cursor:not-allowed;}
  .cn-spinner{
    display:none;width:14px;height:14px;
    border:2px solid rgba(245,233,200,0.35);border-top-color:var(--gold-pale);
    border-radius:50%;animation:cn-spin .6s linear infinite;
  }
  .cn-submit.is-loading .cn-spinner{display:inline-block;}
  @keyframes cn-spin{to{transform:rotate(360deg);}}
  @media (prefers-reduced-motion:reduce){.cn-spinner{animation-duration:1.6s;}}

  .cn-form-message{margin:0 0 16px;font-size:0.86rem;text-align:center;min-height:1px;}
  .cn-form-message--error{color:var(--red);}
  .cn-error-text{
    display:block;margin-top:6px;font-size:0.76rem;color:var(--red);
    letter-spacing:0;text-transform:none;font-weight:400;
  }
  .cn-confirmation{
    text-align:center;padding:clamp(26px,5vw,42px) clamp(18px,4vw,30px);
    background:linear-gradient(150deg,var(--navy-deep) 0%,var(--navy) 100%);
    border-radius:10px;color:var(--white);border:1px solid var(--gold);
  }
  .cn-confirmation h4{
    font-family:'Cinzel',serif;font-weight:700;
    font-size:clamp(1.5rem,4.8vw,2.1rem);color:var(--gold-pale);margin:0 0 14px;
  }
  .cn-confirmation p{
    font-family:'Playfair Display',serif;font-size:clamp(1rem,2.8vw,1.15rem);
    line-height:1.7;margin:0;color:rgba(255,255,255,0.94);
  }

  /* ---------- FOOTER ---------- */
  .cn-footer{
    background:var(--navy-deep);color:rgba(250,247,238,0.6);
    text-align:center;padding:clamp(32px,5vw,48px) 20px;
    border-top:1px solid rgba(201,162,74,0.25);
  }
  .cn-footer-name{
    font-family:'Cinzel',serif;font-weight:700;
    font-size:clamp(1.2rem,3.5vw,1.6rem);color:var(--gold-pale);margin:0 0 8px;
  }
  .cn-footer p{margin:0;font-size:0.7rem;letter-spacing:0.16em;text-transform:uppercase;}

  /* ---------- FIXED RSVP ---------- */
  .cn-fab{
    position:fixed;left:50%;bottom:clamp(16px,3.5vw,26px);transform:translateX(-50%);z-index:45;
    display:inline-flex;align-items:center;gap:9px;
    padding:13px clamp(24px,5vw,34px);border-radius:999px;
    background:linear-gradient(135deg,var(--red) 0%,var(--red-deep) 100%);
    color:var(--gold-pale);
    font-family:'Cinzel',serif;font-weight:600;
    font-size:0.7rem;letter-spacing:0.22em;text-transform:uppercase;
    text-decoration:none;white-space:nowrap;border:1px solid var(--gold);
    box-shadow:0 10px 30px rgba(0,0,0,0.35);
    transition:opacity .3s,visibility .3s,transform .2s;
  }
  .cn-fab:hover{transform:translateX(-50%) translateY(-2px);}
  .cn-fab.is-hidden{opacity:0;visibility:hidden;pointer-events:none;}
  @media (max-width:400px){
    .cn-fab{font-size:0.64rem;letter-spacing:0.16em;padding:12px 22px;}
  }
</style>
</head>
<body>

<nav class="cn-nav">
  <div class="cn-nav-row">
    <a href="#top" class="cn-nav-brand">
      <svg class="cn-nav-crown" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M8 46L4 20L18 30L32 12L46 30L60 20L56 46H8Z" fill="#c9a24a"/>
        <rect x="8" y="46" width="48" height="7" rx="1.5" fill="#c9a24a"/>
        <circle cx="32" cy="12" r="3.5" fill="#8e1b2e"/>
        <circle cx="18" cy="30" r="2.6" fill="#8e1b2e"/>
        <circle cx="46" cy="30" r="2.6" fill="#8e1b2e"/>
      </svg>
      <span>Dr. Casmiar Nwaigwe</span>
    </a>
    <button type="button" class="cn-menu-toggle" id="cn-toggle" aria-label="Open menu">Menu</button>
  </div>
  <div class="cn-nav-kente" aria-hidden="true">
    <?php for ($i = 0; $i < 40; $i++): ?><span></span><?php endfor; ?>
  </div>
</nav>

<div class="cn-overlay" id="cn-overlay"></div>
<aside class="cn-drawer" id="cn-drawer" aria-hidden="true">
  <div class="cn-drawer-head">
    <p class="cn-drawer-title">Menu</p>
    <button type="button" class="cn-drawer-cancel" id="cn-drawer-cancel" aria-label="Close menu">&times;</button>
  </div>
  <a href="#top">Home</a>
  <a href="#events">Celebration</a>
  <a href="#hotel">Hotel &amp; Reservation</a>
  <a href="#rsvp">RSVP</a>
</aside>

<header class="cn-hero" id="top">
  <div class="cn-hero-pattern" style="background-image:url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22140%22 height=%22140%22><g fill=%22none%22 stroke=%22%23c9a24a%22 stroke-width=%221.2%22><circle cx=%2270%22 cy=%2270%22 r=%2250%22/><circle cx=%2270%22 cy=%2270%22 r=%2230%22/><path d=%22M70 20 L70 120 M20 70 L120 70%22/></g></svg>');"></div>
  <div class="cn-hero-vignette"></div>

  <!-- Corner ornaments: stylised Adinkra-inspired scroll motif, echoed on all four corners via CSS mirroring -->
  <svg class="cn-hero-corner cn-hero-corner--tl" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M0 0 L140 0 L140 8 Q60 8 8 60 Q8 60 8 140 L0 140 Z" fill="none" stroke="#c9a24a" stroke-width="1.4"/>
    <path d="M0 22 Q70 22 22 70" stroke="#c9a24a" stroke-width="1.1" fill="none"/>
    <circle cx="22" cy="22" r="4" fill="#8e1b2e"/>
    <circle cx="46" cy="12" r="2.4" fill="#e8cd85"/>
    <circle cx="12" cy="46" r="2.4" fill="#e8cd85"/>
  </svg>
  <svg class="cn-hero-corner cn-hero-corner--tr" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M0 0 L140 0 L140 8 Q60 8 8 60 Q8 60 8 140 L0 140 Z" fill="none" stroke="#c9a24a" stroke-width="1.4"/>
    <path d="M0 22 Q70 22 22 70" stroke="#c9a24a" stroke-width="1.1" fill="none"/>
    <circle cx="22" cy="22" r="4" fill="#8e1b2e"/>
    <circle cx="46" cy="12" r="2.4" fill="#e8cd85"/>
    <circle cx="12" cy="46" r="2.4" fill="#e8cd85"/>
  </svg>
  <svg class="cn-hero-corner cn-hero-corner--bl" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M0 0 L140 0 L140 8 Q60 8 8 60 Q8 60 8 140 L0 140 Z" fill="none" stroke="#c9a24a" stroke-width="1.4"/>
    <path d="M0 22 Q70 22 22 70" stroke="#c9a24a" stroke-width="1.1" fill="none"/>
    <circle cx="22" cy="22" r="4" fill="#8e1b2e"/>
    <circle cx="46" cy="12" r="2.4" fill="#e8cd85"/>
    <circle cx="12" cy="46" r="2.4" fill="#e8cd85"/>
  </svg>
  <svg class="cn-hero-corner cn-hero-corner--br" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M0 0 L140 0 L140 8 Q60 8 8 60 Q8 60 8 140 L0 140 Z" fill="none" stroke="#c9a24a" stroke-width="1.4"/>
    <path d="M0 22 Q70 22 22 70" stroke="#c9a24a" stroke-width="1.1" fill="none"/>
    <circle cx="22" cy="22" r="4" fill="#8e1b2e"/>
    <circle cx="46" cy="12" r="2.4" fill="#e8cd85"/>
    <circle cx="12" cy="46" r="2.4" fill="#e8cd85"/>
  </svg>

  <div class="cn-hero-inner cn-wrap">
    <svg class="cn-crown-mark" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M8 46L4 20L18 30L32 12L46 30L60 20L56 46H8Z" fill="#c9a24a" stroke="#e8cd85" stroke-width="1"/>
      <rect x="8" y="46" width="48" height="7" rx="1.5" fill="#c9a24a"/>
      <circle cx="32" cy="12" r="3.5" fill="#8e1b2e"/>
      <circle cx="18" cy="30" r="2.6" fill="#8e1b2e"/>
      <circle cx="46" cy="30" r="2.6" fill="#8e1b2e"/>
    </svg>
    <div class="cn-portrait-frame">
      <img src="assets/casmiar.png" alt="Dr. Casmiar Nwaigwe" width="220" height="220">
    </div>
    <p class="cn-announce">In Celebration Of</p>
    <h1 class="cn-names">Dr. Casmiar Nwaigwe</h1>
    <p class="cn-subtitle">Birthday Celebration</p>
    <span class="cn-theme-badge">African Royalty</span>

    <p class="cn-date">Saturday, 19th December 2026</p>
    <p class="cn-venue">Oriental Hotel Lagos &bull; 3 Lekki Road, Victoria Island, Lagos, Nigeria</p>

    <div class="cn-countdown" id="cn-countdown">
      <div class="cn-count-box"><span class="cn-count-num" id="cn-days">00</span><span class="cn-count-lbl">Days</span></div>
      <div class="cn-count-box"><span class="cn-count-num" id="cn-hours">00</span><span class="cn-count-lbl">Hours</span></div>
      <div class="cn-count-box"><span class="cn-count-num" id="cn-minutes">00</span><span class="cn-count-lbl">Minutes</span></div>
      <div class="cn-count-box"><span class="cn-count-num" id="cn-seconds">00</span><span class="cn-count-lbl">Seconds</span></div>
    </div>

    <a href="#rsvp" class="cn-hero-cta">RSVP Now</a>
  </div>
</header>

<section class="cn-section" id="events">
  <div class="cn-wrap">
    <div class="cn-section-head">
      <div class="cn-divider">
        <div class="cn-divider-line"></div>
        <svg viewBox="0 0 24 24" fill="none"><path d="M3 20L2 9L7 13L12 5L17 13L22 9L21 20H3Z" fill="#c9a24a"/><rect x="3" y="20" width="18" height="2" rx="0.5" fill="#c9a24a"/></svg>
        <div class="cn-divider-line"></div>
      </div>
      <p class="cn-eyebrow">Save The Date</p>
      <h2 class="cn-title">The Celebration</h2>
      <p class="cn-lead">Please join us as we celebrate the birthday of our most cherished husband and father, in grand African Royalty style.</p>
    </div>
    <div class="cn-events">
      <div class="cn-event">
        <svg class="cn-event-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="17" rx="2" stroke="#8e1b2e" stroke-width="1.6"/><path d="M3 9H21" stroke="#8e1b2e" stroke-width="1.6"/><path d="M8 2V6M16 2V6" stroke="#8e1b2e" stroke-width="1.6" stroke-linecap="round"/></svg>
        <h3>Date</h3>
        <p><strong>Saturday, 19th December 2026</strong></p>
        <p>Kindly join us to make the day truly memorable.</p>
      </div>
      <div class="cn-event">
        <svg class="cn-event-icon" viewBox="0 0 24 24" fill="none"><path d="M12 21S4 14.5 4 9.5C4 5.9 6.9 3 10.5 3C11.9 3 13.2 3.6 14 4.6C14.8 3.6 16.1 3 17.5 3C21.1 3 24 5.9 24 9.5" stroke="#8e1b2e" stroke-width="1.6"/><circle cx="12" cy="10" r="8" stroke="#8e1b2e" stroke-width="1.6"/><path d="M12 6V14M8 10H16" stroke="#8e1b2e" stroke-width="1.6"/></svg>
        <h3>Venue</h3>
        <p><strong>Oriental Hotel Lagos</strong></p>
        <p>3 Lekki Road, Victoria Island, Lagos, Nigeria</p>
      </div>
      <div class="cn-event">
        <svg class="cn-event-icon" viewBox="0 0 24 24" fill="none"><path d="M12 2L14.5 8.5L21 9L16 13.5L17.5 20L12 16.5L6.5 20L8 13.5L3 9L9.5 8.5L12 2Z" stroke="#8e1b2e" stroke-width="1.6" stroke-linejoin="round"/></svg>
        <h3>Theme</h3>
        <p><strong>African Royalty</strong></p>
        <p>Colours of the day: Blue, Red &amp; White</p>
      </div>
      <div class="cn-event">
        <svg class="cn-event-icon" viewBox="0 0 24 24" fill="none"><path d="M12 3L12 21" stroke="#8e1b2e" stroke-width="1.6" stroke-linecap="round"/><path d="M8 3.5C8 5.4 9.8 7 12 7C14.2 7 16 5.4 16 3.5" stroke="#8e1b2e" stroke-width="1.6" stroke-linecap="round"/><circle cx="12" cy="3" r="1.6" fill="#8e1b2e"/><path d="M8 21H16" stroke="#8e1b2e" stroke-width="1.6" stroke-linecap="round"/></svg>
        <h3>Attire</h3>
        <p><strong>Royal &amp; Regal</strong></p>
        <p>Come adorned in your finest African royalty-inspired outfit.</p>
      </div>
    </div>
  </div>
</section>

<section class="cn-section cn-section--navy" id="hotel">
  <div class="cn-wrap">
    <div class="cn-section-head">
      <div class="cn-divider">
        <div class="cn-divider-line"></div>
        <svg viewBox="0 0 24 24" fill="none"><ellipse cx="12" cy="7" rx="7" ry="4" stroke="#e8cd85" stroke-width="1.5"/><path d="M5 7V13C5 15.2 8.1 17 12 17C15.9 17 19 15.2 19 13V7" stroke="#e8cd85" stroke-width="1.5"/><path d="M12 17V21M9 21H15" stroke="#e8cd85" stroke-width="1.5" stroke-linecap="round"/></svg>
        <div class="cn-divider-line"></div>
      </div>
      <p class="cn-eyebrow">Hotel Reservation &amp; RSVP</p>
      <h2 class="cn-title">We Look Forward To Celebrating With You</h2>
    </div>

    <div class="cn-hotel">
      <div class="cn-hotel-card">
        <h3>Discounted Room Rates</h3>
        <div class="cn-rate-row">
          <span class="cn-rate-name">Standard Room<br><span class="cn-rate-note">per night</span></span>
          <span class="cn-rate-price">&#8358;190,000</span>
        </div>
        <div class="cn-rate-row">
          <span class="cn-rate-name">Classic Superior<br><span class="cn-rate-note">per night</span></span>
          <span class="cn-rate-price">&#8358;270,000</span>
        </div>
        <div class="cn-code">
          To book at the discounted price, use the code:<br>
          <strong>&ldquo;Dr. Casmiar&rsquo;s Birthday Celebration&rdquo;</strong>
        </div>
      </div>

      <div class="cn-hotel-card">
        <h3>To Reserve Your Room</h3>
        <ul class="cn-contact-list">
          <li>
            <svg viewBox="0 0 24 24" fill="none"><path d="M22 16.92V19a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 1h2.09a2 2 0 0 1 2 1.72c.12.86.32 1.7.6 2.51a2 2 0 0 1-.45 2.11L7.09 8.6a16 16 0 0 0 6 6l1.26-1.26a2 2 0 0 1 2.11-.45c.81.28 1.65.48 2.51.6a2 2 0 0 1 1.72 2.03Z" stroke="currentColor" stroke-width="1.6"/></svg>
            <span>Call: <a href="tel:+2342012806600">+234 (0) 201 280 6600</a></span>
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z" stroke="currentColor" stroke-width="1.6"/></svg>
            <span>WhatsApp: <a href="https://wa.me/2347026960065">+234 702 696 0065</a></span>
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none"><rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="m22 6-10 7L2 6" stroke="currentColor" stroke-width="1.6"/></svg>
            <span>Email: <a href="mailto:reservation@lagosoriental.com">reservation@lagosoriental.com</a></span>
          </li>
        </ul>
      </div>
    </div>

    <p class="cn-hotel-note">Kindly confirm your attendance and hotel reservation as soon as possible to assist us with planning.<br>Your presence will make the celebration even more special!</p>
  </div>
</section>

<section class="cn-notice">
  <div class="cn-wrap">
    <p class="cn-notice-kicker">Please Note</p>
    <p class="cn-notice-main">African Royalty Attire</p>
    <div class="cn-notice-rule"></div>
    <p class="cn-notice-kicker">Colours Of The Day</p>
    <p class="cn-notice-date">Blue &middot; Red &middot; White</p>
  </div>
</section>

<section class="cn-section cn-section--tint" id="rsvp">
  <div class="cn-section-head">
    <div class="cn-divider">
      <div class="cn-divider-line"></div>
      <svg viewBox="0 0 24 24" fill="none"><path d="M12 21S4 14.5 4 9.5C4 5.9 6.9 3 10.5 3C11.9 3 13.2 3.6 14 4.6C14.8 3.6 16.1 3 17.5 3C21.1 3 24 5.9 24 9.5C24 14.5 12 21 12 21Z" fill="#8e1b2e" transform="scale(0.85) translate(1.8,1.2)"/></svg>
      <div class="cn-divider-line"></div>
    </div>
    <p class="cn-eyebrow">Kindly Respond</p>
    <h2 class="cn-title">Confirm Your Attendance</h2>
  </div>

  <div class="cn-card">
    <div class="cn-confirmation" id="cn-confirmation" <?= $submitted ? '' : 'hidden' ?>>
      <h4>Thank You!</h4>
      <p>Your RSVP has been received.<br>We can&rsquo;t wait to celebrate with you.</p>
    </div>

    <div <?= $submitted ? 'hidden' : '' ?> id="cn-form-shell">
      <h3 class="cn-card-heading">RSVP</h3>
      <span class="cn-card-sub">Dr. Casmiar Nwaigwe &bull; 19 December 2026</span>

      <p class="cn-form-message <?= $failed ? 'cn-form-message--error' : '' ?>" id="cn-form-message"><?= $failed ? 'Something went wrong. Please check your details and try again.' : '' ?></p>

      <form class="cn-rsvp-form" id="cn-rsvp-form" action="<?= htmlspecialchars($actionUrl, ENT_QUOTES) ?>" method="post" novalidate>
        <input type="hidden" name="form_slug" value="<?= htmlspecialchars(PWS_FORM_SLUG, ENT_QUOTES) ?>">

        <div class="cn-field">
          <label for="title">Title</label>
          <select id="title" name="title">
            <option value="">-</option>
            <option>Mr</option><option>Mrs</option><option>Miss</option>
            <option>Ms</option><option>Dr</option><option>Chief</option>
            <option>Engr</option><option>Prof</option><option>Rev</option>
            <option>Pastor</option><option>Barr</option>
          </select>
        </div>

        <div class="cn-field">
          <label for="full_name">Full Name <span class="cn-required">*</span></label>
          <input type="text" id="full_name" name="full_name" required>
        </div>

        <div class="cn-field">
          <label for="phone">Phone Number <span class="cn-required">*</span></label>
          <input type="tel" id="phone" name="phone" required>
        </div>

        <div class="cn-field">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email">
        </div>

        <fieldset class="cn-fieldset">
          <legend>Will you be attending? <span class="cn-required">*</span></legend>
          <div class="cn-radio-group">
            <label><input type="radio" name="attending" value="yes" required> Yes</label>
            <label><input type="radio" name="attending" value="no"> No</label>
          </div>
        </fieldset>

        <div class="cn-field" hidden data-toggle="attending:yes">
          <label for="guest_count">Number Attending</label>
          <select id="guest_count" name="guest_count">
            <option value="">-</option>
            <option value="just_me">Just me</option>
            <option value="plus_one">Me plus one</option>
            <option value="family">Family</option>
          </select>
        </div>

        <div class="cn-field" hidden data-toggle="guest_count:plus_one,family">
          <label for="guest_names">Guest Name(s)</label>
          <input type="text" id="guest_names" name="guest_names" placeholder="Full name(s) of your guest(s)">
        </div>

        <div class="cn-field" hidden data-toggle="attending:yes">
          <label for="hotel_reservation">Do you require a hotel room reservation?</label>
          <select id="hotel_reservation" name="hotel_reservation">
            <option value="">-</option>
            <option value="standard">Yes &mdash; Standard Room</option>
            <option value="superior">Yes &mdash; Classic Superior</option>
            <option value="no">No, thank you</option>
          </select>
        </div>

        <div class="cn-field" hidden data-toggle="attending:no">
          <label for="message">Leave A Message For Dr. Casmiar</label>
          <textarea id="message" name="message" placeholder="Your good wishes for Dr. Casmiar Nwaigwe..."></textarea>
        </div>

        <button type="submit" class="cn-submit" id="cn-submit">
          <span>Submit</span>
          <span class="cn-spinner" aria-hidden="true"></span>
        </button>
      </form>
    </div>
  </div>
</section>

<a href="#rsvp" class="cn-fab" id="cn-fab">RSVP</a>

<footer class="cn-footer">
  <p class="cn-footer-name">Dr. Casmiar Nwaigwe</p>
  <p>19 December 2026 &bull; Oriental Hotel, Lagos</p>
</footer>

<script>
(function(){
  // ---------- DRAWER ----------
  const toggle = document.getElementById('cn-toggle');
  const drawer = document.getElementById('cn-drawer');
  const overlay = document.getElementById('cn-overlay');
  const cancel = document.getElementById('cn-drawer-cancel');

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

  // ---------- COUNTDOWN ----------
  const eventDate = new Date('2026-12-19T00:00:00').getTime();
  const daysEl = document.getElementById('cn-days');
  const hoursEl = document.getElementById('cn-hours');
  const minutesEl = document.getElementById('cn-minutes');
  const secondsEl = document.getElementById('cn-seconds');
  const countdownEl = document.getElementById('cn-countdown');

  function updateCountdown(){
    const diff = eventDate - Date.now();
    if (diff <= 0){
      countdownEl.innerHTML = '<span style="font-family:\'Playfair Display\',serif;font-style:italic;font-weight:700;font-size:2.2rem;color:#e8cd85;">Celebrating Today!</span>';
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
  const form = document.getElementById('cn-rsvp-form');
  const formShell = document.getElementById('cn-form-shell');
  const confirmationEl = document.getElementById('cn-confirmation');
  const messageEl = document.getElementById('cn-form-message');
  const fab = document.getElementById('cn-fab');
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
    form.querySelectorAll('.cn-error-text').forEach(function(el){ el.remove(); });
    messageEl.className = 'cn-form-message';
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
          messageEl.className = 'cn-form-message cn-form-message--error';
          messageEl.textContent = 'Please fix the highlighted fields and try again.';
          const errors = (data && data.errors) || {};
          Object.keys(errors).forEach(function(field){
            const input = form.querySelector('[name="' + field + '"]');
            const text = document.createElement('span');
            text.className = 'cn-error-text';
            text.textContent = errors[field];
            if (input && input.closest('.cn-field')){
              input.closest('.cn-field').appendChild(text);
            } else if (input && input.closest('.cn-fieldset')){
              input.closest('.cn-fieldset').appendChild(text);
            }
          });
        }
      })
      .catch(function(){
        messageEl.className = 'cn-form-message cn-form-message--error';
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
