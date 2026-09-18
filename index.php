<?php
declare(strict_types=1);
require_once __DIR__ . '/rsvp-core/config.php';

/**
 * Form directory / index.
 *
 * Scans this folder for form pages rather than keeping a hardcoded list, so a
 * new form appears here automatically the moment its directory is added. A
 * folder counts as a form page if it contains an index.php that declares a
 * PWS_FORM_SLUG constant.
 *
 * Title comes from the page's <title> tag, with the slug as a fallback.
 */

const PWS_INDEX_SKIP = ['rsvp-core', '.git', 'node_modules'];

function pws_discover_forms(string $root): array
{
    $forms = [];

    foreach (glob($root . '/*', GLOB_ONLYDIR) ?: [] as $dir) {
        $name = basename($dir);
        if (in_array($name, PWS_INDEX_SKIP, true) || str_starts_with($name, '.')) {
            continue;
        }

        $indexFile = $dir . '/index.php';
        if (!is_file($indexFile)) {
            continue;
        }

        // Read the source rather than including it: including would execute the
        // page (DB queries, headers) just to learn its name.
        $source = (string)file_get_contents($indexFile);

        if (!preg_match('/const\s+PWS_FORM_SLUG\s*=\s*[\'"]([^\'"]+)[\'"]/', $source, $slugMatch)) {
            continue;
        }

        $title = $slugMatch[1];
        if (preg_match('/<title>(.*?)<\/title>/is', $source, $titleMatch)) {
            $title = html_entity_decode(trim($titleMatch[1]), ENT_QUOTES, 'UTF-8');
        }

        $forms[] = [
            'dir'   => $name,
            'slug'  => $slugMatch[1],
            'title' => $title,
            'url'   => PWS_SITE_URL . '/' . $name . '/',
            'mtime' => filemtime($indexFile) ?: 0,
        ];
    }

    usort($forms, static fn(array $a, array $b): int => $b['mtime'] <=> $a['mtime']);

    return $forms;
}

$forms = pws_discover_forms(__DIR__);

// Submission counts per slug, so the index doubles as a quick at-a-glance
// dashboard. Degrades to null if the DB is unreachable -- the list still works.
$counts = null;
try {
    $countStmt = pws_pdo()->query(
        'SELECT form_slug, COUNT(*) AS total FROM rsvp_submissions GROUP BY form_slug'
    );
    $counts = [];
    foreach ($countStmt->fetchAll() as $row) {
        $counts[$row['form_slug']] = (int)$row['total'];
    }
} catch (Throwable $e) {
    pws_log('index.php: submission counts unavailable: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>PackageWithSense Forms</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;6..96,500;6..96,700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --navy:#0d1b3e;
    --navy-deep:#070f26;
    --royal:#1e3a8a;
    --gold:#c9a961;
    --gold-pale:#f0e4c4;
    --ink:#1a2236;
    --muted:#5d6577;
    --paper:#f7f8fa;
    --white:#fff;
    --line:#e3e6ee;
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    background:var(--paper);
    color:var(--ink);
    font-family:'Montserrat',sans-serif;
    -webkit-font-smoothing:antialiased;
  }
  .pws-head{
    background:linear-gradient(135deg,var(--navy) 0%,var(--royal) 100%);
    color:var(--white);
    padding:clamp(38px,7vw,64px) 20px;
    text-align:center;
  }
  .pws-head h1{
    font-family:'Bodoni Moda','Bodoni MT','Didot','Hoefler Text',Garamond,'Times New Roman',serif;
    font-weight:700;
    font-size:clamp(1.7rem,5vw,2.6rem);
    margin:0 0 10px;
    color:var(--gold-pale);
    letter-spacing:0.02em;
  }
  .pws-head p{
    margin:0;
    font-size:0.78rem;
    letter-spacing:0.18em;
    text-transform:uppercase;
    color:rgba(255,255,255,0.72);
  }
  .pws-wrap{
    max-width:860px;
    margin:0 auto;
    padding:clamp(28px,5vw,48px) 20px clamp(48px,8vw,72px);
  }
  .pws-search{margin:0 0 16px;}
  .pws-search input{
    width:100%;
    padding:13px 16px;
    border:1px solid var(--line);
    border-radius:6px;
    background:var(--white);
    color:var(--ink);
    font-family:'Montserrat',sans-serif;
    font-size:0.9rem;
    transition:border-color .2s, box-shadow .2s;
  }
  .pws-search input::placeholder{color:#9aa1b2;}
  .pws-search input:focus{
    outline:none;
    border-color:var(--royal);
    box-shadow:0 0 0 3px rgba(30,58,138,0.12);
  }
  .pws-count{
    font-size:0.72rem;
    letter-spacing:0.16em;
    text-transform:uppercase;
    color:var(--muted);
    margin:0 0 18px;
  }
  .pws-noresult{
    background:var(--white);
    border:1px dashed var(--line);
    border-radius:6px;
    padding:30px 20px;
    text-align:center;
    color:var(--muted);
    font-size:0.88rem;
    margin:0;
  }
  .pws-list{list-style:none;margin:0;padding:0;display:grid;gap:14px;}
  .pws-item{
    background:var(--white);
    border:1px solid var(--line);
    border-radius:6px;
    padding:clamp(16px,3vw,22px);
    display:flex;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
    transition:border-color .2s, box-shadow .2s;
  }
  .pws-item:hover{
    border-color:var(--gold);
    box-shadow:0 6px 22px rgba(13,27,62,0.08);
  }
  .pws-item-main{flex:1 1 260px;min-width:0;}
  .pws-item h2{
    font-family:'Bodoni Moda','Bodoni MT','Didot','Hoefler Text',Garamond,'Times New Roman',serif;
    font-size:clamp(1.02rem,2.6vw,1.2rem);
    font-weight:700;
    margin:0 0 6px;
    color:var(--navy);
    overflow-wrap:anywhere;
  }
  .pws-meta{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
    font-size:0.72rem;
    color:var(--muted);
  }
  .pws-slug{
    font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    background:rgba(30,58,138,0.07);
    color:var(--royal);
    padding:2px 8px;
    border-radius:3px;
    font-size:0.7rem;
  }
  .pws-badge{
    background:rgba(201,169,97,0.16);
    color:#8a6d2b;
    padding:2px 8px;
    border-radius:3px;
    font-size:0.7rem;
    font-weight:600;
  }
  .pws-actions{display:flex;gap:8px;flex-wrap:wrap;}
  .pws-btn{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:9px 16px;
    border-radius:4px;
    border:1px solid var(--line);
    background:var(--white);
    color:var(--navy);
    font-family:'Montserrat',sans-serif;
    font-size:0.71rem;
    letter-spacing:0.12em;
    text-transform:uppercase;
    font-weight:600;
    text-decoration:none;
    cursor:pointer;
    white-space:nowrap;
    transition:background .18s, color .18s, border-color .18s;
  }
  .pws-btn:hover{border-color:var(--royal);color:var(--royal);}
  .pws-btn--primary{
    background:linear-gradient(135deg,var(--navy),var(--royal));
    border-color:transparent;
    color:var(--gold-pale);
  }
  .pws-btn--primary:hover{color:var(--white);opacity:0.94;}
  .pws-btn.is-copied{
    border-color:#2f8f5b;
    color:#2f8f5b;
  }
  .pws-empty{
    background:var(--white);
    border:1px dashed var(--line);
    border-radius:6px;
    padding:36px 20px;
    text-align:center;
    color:var(--muted);
    font-size:0.9rem;
  }
  @media (max-width:520px){
    .pws-actions{width:100%;}
    .pws-btn{flex:1 1 auto;justify-content:center;}
  }
</style>
</head>
<body>

<header class="pws-head">
  <h1>PackageWithSense Forms</h1>
  <p>RSVP Form Directory</p>
</header>

<main class="pws-wrap">
  <?php if (!$forms): ?>
    <div class="pws-empty">
      No form pages found yet. Add a directory with an <code>index.php</code> that declares
      a <code>PWS_FORM_SLUG</code> constant and it will appear here automatically.
    </div>
  <?php else: ?>
    <div class="pws-search">
      <input type="search" id="pws-search" placeholder="Search forms by name or slug..." autocomplete="off" aria-label="Search forms">
    </div>
    <p class="pws-count" id="pws-count" data-total="<?= count($forms) ?>"><?= count($forms) ?> form<?= count($forms) === 1 ? '' : 's' ?></p>
    <p class="pws-noresult" id="pws-noresult" hidden>No forms match that search.</p>
    <ul class="pws-list">
      <?php foreach ($forms as $form): ?>
        <li class="pws-item" data-search="<?= htmlspecialchars(mb_strtolower($form['title'] . ' ' . $form['slug'] . ' ' . $form['dir']), ENT_QUOTES) ?>">
          <div class="pws-item-main">
            <h2><?= htmlspecialchars($form['title'], ENT_QUOTES) ?></h2>
            <div class="pws-meta">
              <span class="pws-slug"><?= htmlspecialchars($form['slug'], ENT_QUOTES) ?></span>
              <?php if ($counts !== null): ?>
                <span class="pws-badge"><?= $counts[$form['slug']] ?? 0 ?> RSVP<?= ($counts[$form['slug']] ?? 0) === 1 ? '' : 's' ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="pws-actions">
            <a class="pws-btn pws-btn--primary" href="<?= htmlspecialchars($form['url'], ENT_QUOTES) ?>">Open</a>
            <button type="button" class="pws-btn pws-copy" data-url="<?= htmlspecialchars($form['url'], ENT_QUOTES) ?>">Copy Link</button>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

</main>

<script>
// ---------- SEARCH ----------
(function () {
  const input = document.getElementById('pws-search');
  const countEl = document.getElementById('pws-count');
  const noResult = document.getElementById('pws-noresult');
  if (!input || !countEl) return;

  const items = Array.prototype.slice.call(document.querySelectorAll('.pws-item'));
  const total = parseInt(countEl.getAttribute('data-total'), 10) || items.length;

  function label(n) {
    return n + ' form' + (n === 1 ? '' : 's');
  }

  input.addEventListener('input', function () {
    const q = input.value.trim().toLowerCase();
    let shown = 0;

    items.forEach(function (item) {
      const match = q === '' || (item.getAttribute('data-search') || '').indexOf(q) !== -1;
      item.hidden = !match;
      if (match) shown++;
    });

    countEl.textContent = q === '' ? label(total) : label(shown) + ' of ' + total;
    if (noResult) noResult.hidden = shown !== 0;
  });
})();

// ---------- COPY LINK ----------
document.querySelectorAll('.pws-copy').forEach(function (btn) {
  btn.addEventListener('click', function () {
    const url = btn.getAttribute('data-url');
    const done = function () {
      const original = btn.textContent;
      btn.textContent = 'Copied';
      btn.classList.add('is-copied');
      setTimeout(function () {
        btn.textContent = original;
        btn.classList.remove('is-copied');
      }, 1600);
    };

    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(url).then(done).catch(fallback);
    } else {
      fallback();
    }

    // clipboard API needs HTTPS; plain http://localhost dev needs this path.
    function fallback() {
      const ta = document.createElement('textarea');
      ta.value = url;
      ta.setAttribute('readonly', '');
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); done(); } catch (e) { window.prompt('Copy this link:', url); }
      document.body.removeChild(ta);
    }
  });
});
</script>

</body>
</html>
