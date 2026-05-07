<?php
// ---------------------------------------------------------------------------
// Scan media directories and build playlist sorted by filename.
// Rename files with numeric prefix to control order:
//   01_intro.jpg  ->  02_demo.mp4  ->  03_cierre.png
// ---------------------------------------------------------------------------

$img_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$vid_exts = ['mp4', 'webm', 'ogv', 'ogg'];
$vid_mime = [
    'mp4'  => 'video/mp4',
    'webm' => 'video/webm',
    'ogv'  => 'video/ogg',
    'ogg'  => 'video/ogg',
];

$media = [];

function scan_media_dir($dir, $type, $extensions, &$media, $mime_map = []) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $file) {
        if ($file[0] === '.') continue;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, $extensions)) continue;
        $src = ($type === 'image' ? 'assets/images/' : 'assets/videos/')
             . implode('/', array_map('rawurlencode', explode('/', $file)));
        $entry = ['type' => $type, 'src' => $src, 'file' => $file];
        if ($type === 'video') {
            $entry['mime'] = $mime_map[$ext] ?? 'video/mp4';
        }
        $media[$file] = $entry;
    }
}

scan_media_dir(__DIR__ . '/assets/images/', 'image', $img_exts, $media);
scan_media_dir(__DIR__ . '/assets/videos/', 'video', $vid_exts, $media, $vid_mime);

ksort($media);
$playlist  = array_values($media);
$has_media = !empty($playlist);

// ?duration=N  sets image display time in seconds (default: 5, range: 1–120)
$img_secs = (int) ($_GET['duration'] ?? 5);
$img_ms   = max(1000, min(120000, $img_secs * 1000));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amivtac</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --red:    #c0392b;
      --fadems: 900ms;
    }

    html, body {
      width: 100%;
      height: 100%;
      overflow: hidden;
      background: #000;
      font-family: 'Segoe UI', system-ui, sans-serif;
      cursor: none;
    }

    /* ── Slides ─────────────────────────────────────────────────── */
    #slideshow {
      position: relative;
      width: 100vw;
      height: 100vh;
    }

    .slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity var(--fadems) ease-in-out;
    }

    .slide.active { opacity: 1; }

    .slide img,
    .slide video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    /* ── Progress bar (images only) ─────────────────────────────── */
    #progress {
      position: fixed;
      bottom: 0;
      left: 0;
      height: 5px;
      width: 0%;
      background: var(--red);
      z-index: 300;
    }

    /* ── Dot / counter navigation ───────────────────────────────── */
    #nav {
      position: fixed;
      bottom: 18px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      align-items: center;
      gap: 8px;
      z-index: 300;
    }

    .dot {
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.35);
      transition: background 0.3s, transform 0.3s;
      flex-shrink: 0;
    }

    .dot.active {
      background: #fff;
      transform: scale(1.4);
    }

    #counter {
      color: rgba(255, 255, 255, 0.75);
      font-size: 0.85rem;
      letter-spacing: 1px;
      font-variant-numeric: tabular-nums;
    }

    /* ── Pause indicator ────────────────────────────────────────── */
    #pause-badge {
      position: fixed;
      top: 28px;
      right: 32px;
      background: rgba(0, 0, 0, 0.55);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      padding: 8px 18px;
      color: #fff;
      font-size: 0.9rem;
      letter-spacing: 1px;
      opacity: 0;
      transition: opacity 0.3s;
      pointer-events: none;
      z-index: 300;
    }

    #pause-badge.show { opacity: 1; }

    /* ── Empty state ────────────────────────────────────────────── */
    #empty {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: #fff;
      text-align: center;
      gap: 28px;
      padding: 40px;
    }

    #empty .brand {
      font-size: 3.5rem;
      font-weight: 800;
      color: var(--red);
      letter-spacing: 4px;
    }

    #empty h2 {
      font-size: 1.25rem;
      font-weight: 400;
      color: #ccc;
    }

    .hint-box {
      background: #111;
      border: 1px solid #2a2a2a;
      border-radius: 12px;
      padding: 28px 36px;
      text-align: left;
      color: #999;
      line-height: 2.2;
      font-size: 0.95rem;
      max-width: 480px;
    }

    .hint-box strong { color: #e0e0e0; }
    .hint-box code {
      background: #222;
      color: #ddd;
      padding: 2px 10px;
      border-radius: 5px;
      font-size: 0.88rem;
      font-family: 'Courier New', monospace;
    }
  </style>
</head>
<body>

<?php if (!$has_media): ?>

<div id="empty">
  <div class="brand">AMIVTAC</div>
  <h2>Agrega tus archivos para iniciar el loop</h2>
  <div class="hint-box">
    <strong>Imágenes</strong> → <code>assets/images/</code><br>
    Formatos: <code>.jpg</code> <code>.png</code> <code>.webp</code> <code>.gif</code>
    <br><br>
    <strong>Videos</strong> → <code>assets/videos/</code><br>
    Formatos: <code>.mp4</code> <code>.webm</code>
    <br><br>
    <strong>Orden:</strong> renombra con prefijo numérico<br>
    <code>01_bienvenida.jpg</code> <code>02_video.mp4</code> <code>03_logo.png</code>
    <br><br>
    <strong>Duración por imagen:</strong> <code>?duration=8</code> (segundos)
  </div>
</div>

<?php else: ?>

<div id="slideshow">
  <?php foreach ($playlist as $i => $item): ?>
  <div class="slide<?= $i === 0 ? ' active' : '' ?>"
       data-type="<?= $item['type'] ?>">
    <?php if ($item['type'] === 'image'): ?>
      <img
        src="<?= htmlspecialchars($item['src']) ?>"
        alt="Amivtac slide <?= $i + 1 ?>"
        loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
    <?php else: ?>
      <video preload="<?= $i === 0 ? 'auto' : 'none' ?>" muted playsinline>
        <source src="<?= htmlspecialchars($item['src']) ?>"
                type="<?= htmlspecialchars($item['mime']) ?>">
      </video>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<!-- Progress bar for image slides -->
<div id="progress"></div>

<!-- Dot nav (≤ 12 slides) or counter (> 12 slides) -->
<div id="nav">
  <?php if (count($playlist) <= 12): ?>
    <?php foreach ($playlist as $i => $_): ?>
    <div class="dot<?= $i === 0 ? ' active' : '' ?>" data-idx="<?= $i ?>"></div>
    <?php endforeach; ?>
  <?php else: ?>
    <span id="counter">1 / <?= count($playlist) ?></span>
  <?php endif; ?>
</div>

<div id="pause-badge">⏸ PAUSA</div>

<script>
(function () {
  const IMAGE_MS   = <?= $img_ms ?>;
  const FADE_MS    = 900;
  const TOTAL      = <?= count($playlist) ?>;
  const USE_DOTS   = TOTAL <= 12;
  const TYPES      = <?= json_encode(array_column($playlist, 'type')) ?>;

  const slides    = document.querySelectorAll('.slide');
  const progress  = document.getElementById('progress');
  const navEl     = document.getElementById('nav');
  const dots      = navEl.querySelectorAll('.dot');
  const counter   = document.getElementById('counter');
  const pauseBadge = document.getElementById('pause-badge');

  let current        = 0;
  let paused         = false;
  let imageTimer     = null;
  let progStart      = null;
  let progRemaining  = 0;
  let progRafId      = null;

  // ── Progress bar ──────────────────────────────────────────────────
  function startProgress(duration) {
    progRemaining = duration;
    progStart     = performance.now();
    progress.style.transition = 'none';
    progress.style.width = '0%';

    requestAnimationFrame(() => {
      progress.style.transition = `width ${duration}ms linear`;
      progress.style.width = '100%';
    });

    imageTimer = setTimeout(nextSlide, duration);
  }

  function pauseProgress() {
    const elapsed  = performance.now() - progStart;
    progRemaining  = Math.max(0, progRemaining - elapsed);
    clearTimeout(imageTimer);

    const pct = ((IMAGE_MS - progRemaining) / IMAGE_MS) * 100;
    progress.style.transition = 'none';
    progress.style.width = pct + '%';
  }

  function resumeProgress() {
    progStart = performance.now();
    progress.style.transition = `width ${progRemaining}ms linear`;
    progress.style.width = '100%';
    imageTimer = setTimeout(nextSlide, progRemaining);
  }

  function clearProgress() {
    clearTimeout(imageTimer);
    progress.style.transition = 'none';
    progress.style.width = '0%';
  }

  // ── Navigation helpers ────────────────────────────────────────────
  function updateNav(idx) {
    if (USE_DOTS) {
      dots.forEach(d => d.classList.toggle('active', +d.dataset.idx === idx));
    } else {
      counter.textContent = `${idx + 1} / ${TOTAL}`;
    }
  }

  // ── Show a slide by index ─────────────────────────────────────────
  function showSlide(idx) {
    const next = ((idx % TOTAL) + TOTAL) % TOTAL;

    // Deactivate current
    const prevSlide = slides[current];
    prevSlide.classList.remove('active');
    const prevVid = prevSlide.querySelector('video');
    if (prevVid) { prevVid.pause(); prevVid.currentTime = 0; }

    clearProgress();
    current = next;
    updateNav(current);

    const slide = slides[current];
    slide.classList.add('active');

    if (TYPES[current] === 'video') {
      const vid = slide.querySelector('video');
      vid.preload = 'auto';
      vid.load();
      vid.play().catch(() => {
        // Autoplay blocked: fall back to image duration
        if (!paused) startProgress(IMAGE_MS);
      });
      vid.onended = () => { if (!paused) nextSlide(); };
    } else {
      if (!paused) startProgress(IMAGE_MS);
    }
  }

  function nextSlide() { showSlide(current + 1); }
  function prevSlide() { showSlide(current - 1); }

  // ── Init first slide without re-triggering the transition ─────────
  function initFirst() {
    updateNav(0);
    if (TYPES[0] === 'video') {
      const vid = slides[0].querySelector('video');
      vid.play().catch(() => startProgress(IMAGE_MS));
      vid.onended = () => { if (!paused) nextSlide(); };
    } else {
      startProgress(IMAGE_MS);
    }
  }

  // ── Pause / resume ────────────────────────────────────────────────
  function togglePause() {
    paused = !paused;
    pauseBadge.classList.toggle('show', paused);

    const vid = slides[current].querySelector('video');
    if (paused) {
      if (vid) { vid.pause(); }
      else { pauseProgress(); }
    } else {
      if (vid) { vid.play(); }
      else { resumeProgress(); }
    }
  }

  // ── Events ───────────────────────────────────────────────────────
  document.addEventListener('keydown', e => {
    switch (e.key) {
      case 'ArrowRight':
      case 'ArrowDown':
        if (paused) togglePause();
        nextSlide();
        break;
      case 'ArrowLeft':
      case 'ArrowUp':
        if (paused) togglePause();
        prevSlide();
        break;
      case ' ':
        e.preventDefault();
        togglePause();
        break;
      case 'f':
      case 'F':
        document.fullscreenElement
          ? document.exitFullscreen()
          : document.documentElement.requestFullscreen();
        break;
    }
  });

  // Click to pause
  document.getElementById('slideshow').addEventListener('click', togglePause);

  // Dot click
  dots.forEach(d => {
    d.addEventListener('click', e => {
      e.stopPropagation();
      if (paused) togglePause();
      showSlide(+d.dataset.idx);
    });
  });

  // Touch swipe
  let swipeX = 0;
  document.addEventListener('touchstart', e => {
    swipeX = e.touches[0].clientX;
  }, { passive: true });
  document.addEventListener('touchend', e => {
    const dx = e.changedTouches[0].clientX - swipeX;
    if (Math.abs(dx) > 50) {
      if (paused) togglePause();
      dx < 0 ? nextSlide() : prevSlide();
    }
  });

  initFirst();
})();
</script>

<?php endif; ?>
</body>
</html>
