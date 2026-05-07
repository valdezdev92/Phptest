<?php
// ════════════════════════════════════════════════════════════════════
//  CONFIGURACIÓN GENERAL AMIVTAC
// ════════════════════════════════════════════════════════════════════
$cfg = [
    'org'     => 'AMIVTAC',
    'tagline' => 'Asociación Mexicana de Ingeniería de Vías Terrestres A.C.',
    'year'    => '2026',
    'hashtag' => '#AMIVTAC2026',
    'website' => 'amivtac.org',
    'accent'  => '#27ae60',
];

// ════════════════════════════════════════════════════════════════════
//  OVERLAY POR VIDEO  ← cada video muestra la info de su evento
// ════════════════════════════════════════════════════════════════════
$video_overlays = [

    'Ambiental.mp4' => [
        'acc'       => '#1f8c55',
        'committee' => 'AMIVTAC · COMITÉ DE MEDIO AMBIENTE',
        'number'    => '',
        'bars'      => true,
        'title'     => "SEMINARIO INTERNACIONAL\nDE IMPACTO AMBIENTAL",
        'subtitle'  => '"Movilidad en la Selva Maya"',
        'date_main' => '28-29 Mayo',
        'date_sub'  => '2026',
        'loc_main'  => 'Campeche',
        'loc_sub'   => 'México · Patrimonio UNESCO',
        'topics'    => ['Selva Maya sin Fronteras', 'Pasos de Fauna', 'Cooperación Trinacional'],
        'tag'       => 'Temas Centrales',
    ],

    'Puentes.mp4' => [
        'acc'       => '#00b4a0',
        'committee' => 'AMIVTAC · COMITÉ TÉCNICO DE PUENTES',
        'number'    => 'VIII',
        'bars'      => false,
        'title'     => "SEMINARIO INTERNACIONAL\nDE PUENTES",
        'subtitle'  => '"Una visión para el futuro"',
        'date_main' => 'Oct. 21-23',
        'date_sub'  => '2026',
        'loc_main'  => 'Mérida, Yucatán',
        'loc_sub'   => 'Centro Internacional de Congresos',
        'topics'    => ['Sostenibilidad y desarrollo responsable', 'Tecnología e innovación', 'Expertos internacionales · PIARC'],
        'tag'       => 'Ejes Temáticos',
    ],

    'Nacional.mp4' => [
        'acc'       => '#5cb85c',
        'committee' => 'XXV REUNIÓN NACIONAL DE VÍAS TERRESTRES',
        'number'    => 'XXV',
        'bars'      => false,
        'title'     => "REUNIÓN NACIONAL\nDE VÍAS TERRESTRES",
        'subtitle'  => '"Infraestructura que Une y Transforma"',
        'date_main' => '15-17 Julio',
        'date_sub'  => '2026',
        'loc_main'  => 'Morelia',
        'loc_sub'   => 'Michoacán, México',
        'topics'    => ['Ingeniería Vial de Alto Nivel', 'Expertos, profesionales y estudiantes', 'Networking y Expo Vías'],
        'tag'       => 'Destacados',
    ],
];

// ════════════════════════════════════════════════════════════════════
//  DIAPOSITIVAS DE CONTENIDO (portada, estadísticas, cierre)
// ════════════════════════════════════════════════════════════════════
$content_slides = [
    'title' => [
        'type'     => 'title',
        'duration' => 9,
        'grad'     => 'linear-gradient(150deg,#0a0a14 0%,#1a0508 100%)',
    ],
    'stats' => [
        'type'     => 'stats',
        'duration' => 9,
        'grad'     => 'linear-gradient(150deg,#0a0a14 0%,#1a0a10 100%)',
        'items'    => [
            ['num' => '3',   'label' => "Eventos\nInternacionales"],
            ['num' => '+20', 'label' => "Ponentes\nEspecialistas"],
            ['num' => '3',   'label' => "Países\nParticipantes"],
        ],
    ],
    'social' => [
        'type'     => 'social',
        'duration' => 7,
        'grad'     => 'linear-gradient(150deg,#0a0a14 0%,#1a0508 100%)',
    ],
];

// ── Escanear imágenes de fondo para diapositivas de contenido ───
function scan_dir_ext(string $dir, array $exts): array {
    if (!is_dir($dir)) return [];
    $out = [];
    foreach (array_diff(scandir($dir), ['.', '..']) as $f)
        if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $exts)) $out[] = $f;
    sort($out);
    return $out;
}

$img_files = scan_dir_ext(__DIR__ . '/assets/images/', ['jpg','jpeg','png','webp','gif']);
$img_n     = count($img_files);
$vid_mime  = ['mp4'=>'video/mp4','webm'=>'video/webm','ogg'=>'video/ogg','ogv'=>'video/ogg'];

// Asignar imagen de fondo a cada slide de contenido
$ci = 0;
foreach ($content_slides as &$s) {
    $s['bg'] = $img_n > 0 ? 'assets/images/' . rawurlencode($img_files[$ci++ % $img_n]) : null;
}
unset($s);

// ════════════════════════════════════════════════════════════════════
//  PLAYLIST: título → videos con overlay → stats → cierre
// ════════════════════════════════════════════════════════════════════
$playlist = [];

// 1. Portada
$playlist[] = ['kind' => 'content', 's' => $content_slides['title']];

// 2. Videos en orden definido (con overlay si existe)
$video_order = ['Ambiental.mp4', 'Puentes.mp4', 'Nacional.mp4'];
foreach ($video_order as $fname) {
    $path = __DIR__ . '/assets/videos/' . $fname;
    if (!file_exists($path)) continue;
    $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
    $playlist[] = [
        'kind'    => 'video',
        'src'     => 'assets/videos/' . rawurlencode($fname),
        'mime'    => $vid_mime[$ext] ?? 'video/mp4',
        'overlay' => $video_overlays[$fname] ?? null,
    ];
}

// 3. Estadísticas y cierre
$playlist[] = ['kind' => 'content', 's' => $content_slides['stats']];
$playlist[] = ['kind' => 'content', 's' => $content_slides['social']];

$js_playlist = json_encode(array_map(fn($p) => [
    'kind'        => $p['kind'],
    'duration'    => $p['kind'] === 'content' ? $p['s']['duration'] * 1000 : null,
    'type'        => $p['kind'] === 'content' ? $p['s']['type'] : 'video',
    'has_overlay' => $p['kind'] === 'video' && !empty($p['overlay']),
], $playlist));

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES|ENT_HTML5, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AMIVTAC 2026</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,300;1,400&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --acc: <?= e($cfg['accent']) ?>;
      --ff:  'Montserrat', 'Segoe UI', Arial, sans-serif;
      --fade: 850ms;
    }

    html, body {
      width: 100%; height: 100%;
      overflow: hidden;
      background: #000;
      font-family: var(--ff);
      cursor: none;
    }

    /* ── Deck ──────────────────────────────────────────────────── */
    #deck { position: relative; width: 100vw; height: 100vh; }

    /* ── Slide base ────────────────────────────────────────────── */
    .slide {
      position: absolute; inset: 0;
      opacity: 0; pointer-events: none; overflow: hidden;
    }
    .slide.is-active { opacity: 1; pointer-events: auto; }
    .slide.is-in     { animation: sIn  var(--fade) cubic-bezier(.4,0,.2,1) both; }
    .slide.is-out    { animation: sOut calc(var(--fade) * .85) cubic-bezier(.4,0,.2,1) both; }

    @keyframes sIn  { from{opacity:0;transform:scale(1.025)} to{opacity:1;transform:scale(1)} }
    @keyframes sOut { from{opacity:1} to{opacity:0} }

    /* ── Background image ──────────────────────────────────────── */
    .s-bg {
      position: absolute; inset: -5%;
      background: center/cover no-repeat;
      transform-origin: center; will-change: transform;
    }
    .is-animated .s-bg { animation: kenBurns 24s ease-out forwards; }
    @keyframes kenBurns {
      0%   { transform: scale(1.12) translate(1.5%,1%); }
      100% { transform: scale(1.00) translate(0%,0%); }
    }

    /* ── Video ─────────────────────────────────────────────────── */
    .s-video {
      position: absolute; inset: 0;
      width: 100%; height: 100%; object-fit: cover;
    }

    /* ── Overlays ──────────────────────────────────────────────── */
    /* Full dark overlay for image slides */
    .s-ov {
      position: absolute; inset: 0;
      background: linear-gradient(155deg,
        rgba(0,0,0,.82) 0%, rgba(0,0,0,.48) 55%, rgba(0,0,0,.70) 100%);
    }

    /* Directional gradient for video slides — keeps video visible */
    .vid-grad {
      position: absolute; inset: 0;
      background:
        linear-gradient(to right,  rgba(0,0,0,.80) 0%, rgba(0,0,0,.45) 45%, rgba(0,0,0,.10) 75%),
        linear-gradient(to top,    rgba(0,0,0,.50) 0%, transparent 35%);
    }

    /* Left accent bar */
    .s-bar {
      position: absolute; left:0; top:8%; bottom:8%;
      width: 6px; background: var(--acc);
      transform: scaleY(0); transform-origin: top;
    }
    .is-animated .s-bar { animation: barIn .55s .1s cubic-bezier(.4,0,.2,1) both; }
    @keyframes barIn { from{transform:scaleY(0)} to{transform:scaleY(1)} }

    /* ── Content wrapper ───────────────────────────────────────── */
    .s-body {
      position: absolute; inset: 0;
      display: flex; align-items: center; justify-content: center;
      padding: 5vh 8vw 5vh 10vw;
      z-index: 2;
    }

    /* ── Animation helpers ─────────────────────────────────────── */
    .a { will-change: transform, opacity; }
    .is-animated .a      { animation: fadeUp   .8s var(--d,.1s) cubic-bezier(.16,1,.3,1) both; }
    .is-animated .a-left { animation: fadeLeft .7s var(--d,.1s) cubic-bezier(.16,1,.3,1) both; }
    .is-animated .a-fade { animation: fadeIn   .9s var(--d,.1s) ease both; }

    @keyframes fadeUp   { from{opacity:0;transform:translateY(26px)}  to{opacity:1;transform:none} }
    @keyframes fadeLeft { from{opacity:0;transform:translateX(-28px)} to{opacity:1;transform:none} }
    @keyframes fadeIn   { from{opacity:0} to{opacity:1} }

    .h-line {
      height: 3px; background: var(--acc);
      transform-origin: left; transform: scaleX(0);
    }
    .is-animated .h-line { animation: lineIn .7s var(--d,.2s) cubic-bezier(.16,1,.3,1) both; }
    @keyframes lineIn { from{transform:scaleX(0)} to{transform:scaleX(1)} }

    .label-tag {
      display: inline-flex; align-items: center;
      border: 1px solid var(--acc); border-radius: 50px;
      padding: .4vh 1.4vw;
      font-size: clamp(.5rem,.85vw,1rem);
      font-weight: 700; letter-spacing: .25em;
      color: var(--acc); text-transform: uppercase;
    }

    /* ════════════════════════════════════════════════════════════
       SLIDE: TITLE
    ════════════════════════════════════════════════════════════ */
    .t-title { flex-direction: column; align-items: center; text-align: center; gap: 0; }
    .t-title .org {
      font-size: clamp(4.5rem,11vw,13rem);
      font-weight: 900; letter-spacing: .14em; color: #fff; line-height: 1;
    }
    .t-title .h-line { width: clamp(5rem,18vw,20rem); margin: 1.8vh auto; }
    .t-title .ev-tag  { margin-bottom: .8vh; }
    .t-title .tagline {
      font-size: clamp(.9rem,1.8vw,2.2rem);
      font-weight: 300; color: rgba(255,255,255,.6); letter-spacing: .04em; margin-top: .5vh;
    }
    .t-title .year-chip {
      margin-top: 2.5vh;
      font-size: clamp(.8rem,1.5vw,1.8rem);
      font-weight: 800; letter-spacing: .18em; color: var(--acc);
    }

    /* ════════════════════════════════════════════════════════════
       OVERLAY DE EVENTO (sobre video e imagen)
    ════════════════════════════════════════════════════════════ */
    .t-event-card { flex-direction: row; align-items: center; gap: 5vw; width: 100%; }

    .ec-left { flex: 1.15; display: flex; flex-direction: column; gap: .9vh; position: relative; }

    /* Doble barra decorativa (Ambiental) */
    .ec-bars { display: flex; gap: .6vw; margin-bottom: .5vh; }
    .ec-bars span {
      display: block;
      width: clamp(8px,1vw,14px); height: clamp(3.5rem,7vh,6rem);
      background: var(--acc); border-radius: 3px;
      transform: scaleY(0); transform-origin: bottom;
    }
    .is-animated .ec-bars span:nth-child(1) { animation: barUp .5s var(--bd1,1.3s) cubic-bezier(.4,0,.2,1) both; }
    .is-animated .ec-bars span:nth-child(2) { animation: barUp .5s var(--bd2,1.5s) cubic-bezier(.4,0,.2,1) both; }
    @keyframes barUp { from{transform:scaleY(0)} to{transform:scaleY(1)} }

    .ec-numeral {
      font-size: clamp(4rem,9vw,12rem); font-weight: 900;
      color: var(--acc); line-height: .9; letter-spacing: .04em;
    }
    .ec-committee {
      font-size: clamp(.5rem,.85vw,1rem); font-weight: 700;
      letter-spacing: .2em; color: var(--acc); text-transform: uppercase;
    }
    .ec-title {
      font-size: clamp(1.6rem,3.8vw,4.8rem); font-weight: 900;
      letter-spacing: .04em; color: #fff; line-height: 1.08; white-space: pre-line;
    }
    .ec-subtitle {
      font-size: clamp(.8rem,1.5vw,1.8rem); font-weight: 300;
      font-style: italic; color: rgba(255,255,255,.7); margin-top: -.2vh;
    }

    /* Chips fecha / sede */
    .ec-chips { display: flex; gap: 1vw; margin-top: .8vh; flex-wrap: wrap; }
    .ec-chip {
      display: flex; align-items: center; gap: .8vw;
      background: rgba(0,0,0,.4);
      backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,.12); border-radius: 10px;
      padding: .7vh 1.2vw;
    }
    .chip-icon { font-size: clamp(.9rem,1.6vw,2rem); line-height: 1; }
    .chip-main { font-size: clamp(.7rem,1.1vw,1.3rem); font-weight: 700; color: #fff; line-height: 1.2; }
    .chip-sub  { font-size: clamp(.55rem,.85vw,1rem); color: rgba(255,255,255,.5); line-height: 1.2; }

    /* Card de temas (glassmorphism) */
    .ec-right {
      flex: .85; display: flex; flex-direction: column; gap: 1.2vh;
      background: rgba(0,0,0,.45);
      backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
      border: 1px solid rgba(255,255,255,.1);
      border-left: 3px solid var(--acc);
      border-radius: 16px; padding: 3vh 2.5vw;
    }
    .topics-heading {
      font-size: clamp(.55rem,.9vw,1.05rem); font-weight: 700;
      letter-spacing: .25em; color: var(--acc); text-transform: uppercase; margin-bottom: .4vh;
    }
    .ec-topic {
      display: flex; align-items: flex-start; gap: .8vw;
      font-size: clamp(.7rem,1.2vw,1.4rem); font-weight: 400;
      color: rgba(255,255,255,.75); line-height: 1.4;
    }
    .ec-topic::before {
      content: ''; display: block;
      width: 7px; height: 7px; border-radius: 50%;
      background: var(--acc); flex-shrink: 0; margin-top: .45em;
    }

    /* ════════════════════════════════════════════════════════════
       SLIDE: STATS
    ════════════════════════════════════════════════════════════ */
    .t-stats { flex-direction: column; align-items: center; text-align: center; gap: 3vh; }
    .t-stats .s-label {
      font-size: clamp(.6rem,1vw,1.2rem); font-weight: 700;
      letter-spacing: .3em; color: var(--acc);
    }
    .stats-row { display: flex; gap: 6vw; justify-content: center; }
    .stat-box  { display: flex; flex-direction: column; align-items: center; gap: .6vh; }
    .stat-num  {
      font-size: clamp(3.5rem,8.5vw,11rem); font-weight: 900;
      color: #fff; line-height: 1; font-variant-numeric: tabular-nums;
    }
    .stat-sep  { height: 3px; width: 2.5rem; background: var(--acc); border-radius: 2px; }
    .stat-lbl  {
      font-size: clamp(.65rem,1.1vw,1.3rem); font-weight: 400;
      color: rgba(255,255,255,.55); white-space: pre-line; text-align: center; line-height: 1.5;
    }

    /* ════════════════════════════════════════════════════════════
       SLIDE: SOCIAL
    ════════════════════════════════════════════════════════════ */
    .t-social { flex-direction: column; align-items: center; text-align: center; gap: 2vh; }
    .t-social .follow {
      font-size: clamp(.6rem,1vw,1.2rem); font-weight: 600;
      letter-spacing: .3em; color: rgba(255,255,255,.5);
    }
    .t-social .hashtag {
      font-size: clamp(2.5rem,6.5vw,8.5rem); font-weight: 900;
      letter-spacing: .04em; color: var(--acc);
    }
    .t-social .h-line { width: clamp(3rem,9vw,11rem); margin: .4vh auto; }
    .t-social .web {
      font-size: clamp(.8rem,1.5vw,1.8rem); font-weight: 300;
      color: rgba(255,255,255,.55); letter-spacing: .08em;
    }

    /* ── Progress bar ──────────────────────────────────────────── */
    #prog {
      position: fixed; top:0; left:0;
      height: 3px; width: 0%; background: var(--acc); z-index: 600;
    }

    /* ── Nav dots ──────────────────────────────────────────────── */
    #nav {
      position: fixed; bottom:20px; left:50%;
      transform: translateX(-50%);
      display: flex; align-items: center; gap: 9px; z-index: 600;
    }
    .dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: rgba(255,255,255,.28);
      transition: background .3s, transform .3s;
    }
    .dot.on { background: var(--acc); transform: scale(1.45); }
    #counter {
      color: rgba(255,255,255,.45); font-family: var(--ff);
      font-size: .78rem; letter-spacing: .08em; font-variant-numeric: tabular-nums;
    }

    /* ── Pause badge ───────────────────────────────────────────── */
    #paused {
      position: fixed; top:24px; right:28px;
      background: rgba(0,0,0,.6); border: 1px solid rgba(255,255,255,.18);
      border-radius: 8px; padding: 8px 20px; color: #fff;
      font-family: var(--ff); font-size: .82rem; letter-spacing: .12em;
      opacity: 0; transition: opacity .25s; pointer-events: none; z-index: 600;
    }
    #paused.show { opacity: 1; }

    /* ── Keyboard hint ─────────────────────────────────────────── */
    #hint {
      position: fixed; bottom: 48px; left: 50%;
      transform: translateX(-50%);
      background: rgba(0,0,0,.5); border-radius: 6px; padding: 5px 14px;
      color: rgba(255,255,255,.45); font-family: var(--ff);
      font-size: .68rem; letter-spacing: .07em;
      opacity: 1; transition: opacity 1.2s; pointer-events: none; z-index: 600;
    }
    #hint.gone { opacity: 0; }
  </style>
</head>
<body>

<div id="deck">
<?php foreach ($playlist as $pi => $p): ?>

  <?php /* ════ VIDEO (con overlay del evento) ════ */ ?>
  <?php if ($p['kind'] === 'video'):
    $ov  = $p['overlay'];
    $acc = $ov ? $ov['acc'] : $cfg['accent'];
  ?>
  <div class="slide" data-kind="video" data-idx="<?= $pi ?>" style="--acc:<?= e($acc) ?>">

    <video class="s-video" muted playsinline preload="<?= $pi === 0 ? 'auto' : 'none' ?>">
      <source src="<?= e($p['src']) ?>" type="<?= e($p['mime']) ?>">
    </video>

    <?php if ($ov): ?>
    <!-- Degradado direccional para leer el texto sin tapar el video -->
    <div class="vid-grad"></div>
    <div class="s-bar"></div>

    <!-- Info del evento sobre el video: aparece ~1.5s después de que inicia -->
    <div class="s-body t-event-card">

      <div class="ec-left">

        <?php if (!empty($ov['bars'])): ?>
        <div class="ec-bars" style="--bd1:1.3s;--bd2:1.5s">
          <span></span><span></span>
        </div>
        <?php endif; ?>

        <div class="ec-committee a" style="--d:1.3s"><?= e($ov['committee']) ?></div>

        <?php if ($ov['number']): ?>
        <div class="ec-numeral a" style="--d:1.5s"><?= e($ov['number']) ?></div>
        <?php endif; ?>

        <div class="ec-title a" style="--d:<?= $ov['number'] ? '1.7' : '1.5' ?>s"><?= e($ov['title']) ?></div>
        <div class="ec-subtitle a" style="--d:<?= $ov['number'] ? '1.9' : '1.7' ?>s"><?= e($ov['subtitle']) ?></div>

        <div class="ec-chips a" style="--d:<?= $ov['number'] ? '2.1' : '1.9' ?>s">
          <div class="ec-chip">
            <span class="chip-icon">📅</span>
            <div>
              <div class="chip-main"><?= e($ov['date_main']) ?></div>
              <div class="chip-sub"><?= e($ov['date_sub']) ?></div>
            </div>
          </div>
          <div class="ec-chip">
            <span class="chip-icon">📍</span>
            <div>
              <div class="chip-main"><?= e($ov['loc_main']) ?></div>
              <div class="chip-sub"><?= e($ov['loc_sub']) ?></div>
            </div>
          </div>
        </div>

      </div>

      <div class="ec-right a-fade a" style="--d:2.0s">
        <div class="topics-heading"><?= e($ov['tag']) ?></div>
        <?php foreach ($ov['topics'] as $t): ?>
        <div class="ec-topic"><?= e($t) ?></div>
        <?php endforeach; ?>
      </div>

    </div>
    <?php endif; ?>

  </div>

  <?php /* ════ CONTENIDO (imagen de fondo) ════ */ ?>
  <?php else: $s = $p['s']; ?>
  <div class="slide"
       data-kind="content"
       data-type="<?= e($s['type']) ?>"
       data-duration="<?= (int)($s['duration'] * 1000) ?>"
       data-idx="<?= $pi ?>"
       style="--acc:<?= e($s['acc'] ?? $cfg['accent']) ?>">

    <div class="s-bg" style="background-image:<?=
      ($s['bg'] ?? null) ? "url('" . e($s['bg']) . "')" : $s['grad']
    ?>"></div>
    <div class="s-ov"></div>
    <div class="s-bar"></div>

    <?php /* ── TITLE ── */ ?>
    <?php if ($s['type'] === 'title'): ?>
    <div class="s-body t-title">
      <div class="label-tag ev-tag a" style="--d:.1s"><?= e($cfg['org']) ?> · Eventos <?= e($cfg['year']) ?></div>
      <div class="org a" style="--d:.3s"><?= e($cfg['org']) ?></div>
      <div class="h-line a" style="--d:.7s"></div>
      <div class="tagline a" style="--d:.9s"><?= e($cfg['tagline']) ?></div>
      <div class="year-chip a" style="--d:1.1s"><?= e($cfg['year']) ?></div>
    </div>

    <?php /* ── STATS ── */ ?>
    <?php elseif ($s['type'] === 'stats'): ?>
    <div class="s-body t-stats">
      <div class="s-label a" style="--d:.1s"><?= e($cfg['org']) ?> · <?= e($cfg['year']) ?></div>
      <div class="stats-row">
        <?php foreach ($s['items'] as $si => $st):
          $d = round(.3 + $si * .22, 2); ?>
        <div class="stat-box a" style="--d:<?= $d ?>s">
          <div class="stat-num" data-target="<?= e($st['num']) ?>">0</div>
          <div class="stat-sep"></div>
          <div class="stat-lbl"><?= e($st['label']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php /* ── SOCIAL ── */ ?>
    <?php elseif ($s['type'] === 'social'): ?>
    <div class="s-body t-social">
      <div class="label-tag a" style="--d:.1s"><?= e($cfg['org']) ?></div>
      <div class="follow a" style="--d:.3s">Síguenos en redes sociales</div>
      <div class="hashtag a" style="--d:.5s"><?= e($cfg['hashtag']) ?></div>
      <div class="h-line a" style="--d:.85s"></div>
      <div class="web a" style="--d:1.05s"><?= e($cfg['website']) ?></div>
    </div>
    <?php endif; ?>

  </div>
  <?php endif; ?>

<?php endforeach; ?>
</div>

<div id="prog"></div>
<div id="nav">
  <?php if (count($playlist) <= 14): ?>
    <?php foreach ($playlist as $pi => $_): ?>
    <div class="dot" data-idx="<?= $pi ?>"></div>
    <?php endforeach; ?>
  <?php else: ?>
    <span id="counter">1 / <?= count($playlist) ?></span>
  <?php endif; ?>
</div>
<div id="paused">⏸ &nbsp;PAUSA</div>
<div id="hint">← → Navegar &nbsp;·&nbsp; Espacio = Pausa &nbsp;·&nbsp; F = Pantalla completa</div>

<script>
(function () {
  const PLAYLIST = <?= $js_playlist ?>;
  const TOTAL    = PLAYLIST.length;
  const USE_DOTS = TOTAL <= 14;
  const FADE_MS  = 800;

  const deck   = document.getElementById('deck');
  const slides = deck.querySelectorAll('.slide');
  const prog   = document.getElementById('prog');
  const navEl  = document.getElementById('nav');
  const dots   = navEl.querySelectorAll('.dot');
  const ctr    = document.getElementById('counter');
  const badge  = document.getElementById('paused');
  const hint   = document.getElementById('hint');

  let cur = 0, paused = false, trans = false;
  let timer = null, progStart = 0, progDur = 0;

  setTimeout(() => hint.classList.add('gone'), 5000);

  // ── Progress bar ────────────────────────────────────────────
  function startProg(ms) {
    progDur = ms; progStart = performance.now();
    prog.style.transition = 'none'; prog.style.width = '0%';
    requestAnimationFrame(() => {
      prog.style.transition = `width ${ms}ms linear`;
      prog.style.width = '100%';
    });
  }
  function pauseProg() {
    clearTimeout(timer);
    const pct = Math.min(100, ((performance.now() - progStart) / progDur) * 100);
    prog.style.transition = 'none'; prog.style.width = pct + '%';
  }
  function resumeProg() {
    const rem = Math.max(300, progDur - (performance.now() - progStart));
    prog.style.transition = `width ${rem}ms linear`; prog.style.width = '100%';
    timer = setTimeout(doNext, rem);
  }
  function clearProg() {
    clearTimeout(timer);
    prog.style.transition = 'none'; prog.style.width = '0%';
  }

  // ── Nav ─────────────────────────────────────────────────────
  function updateNav(i) {
    if (USE_DOTS) dots.forEach(d => d.classList.toggle('on', +d.dataset.idx === i));
    else if (ctr)  ctr.textContent = `${i + 1} / ${TOTAL}`;
  }

  // ── Counter animation ────────────────────────────────────────
  function animCounters(el) {
    el.querySelectorAll('.stat-num[data-target]').forEach(n => {
      const raw = n.dataset.target, pre = raw.startsWith('+') ? '+' : '';
      const max = parseInt(raw.replace(/\D/g,''), 10);
      const t0 = performance.now(), dur = 1800;
      (function tick(now) {
        const p = Math.min((now - t0) / dur, 1);
        n.textContent = pre + Math.round(max * (1 - Math.pow(1 - p, 3)));
        if (p < 1) requestAnimationFrame(tick);
      })(t0);
    });
  }

  // ── Activate ─────────────────────────────────────────────────
  function activate(i) {
    const slide = slides[i];
    const meta  = PLAYLIST[i];

    slide.classList.remove('is-animated');
    void slide.offsetWidth;
    slide.classList.add('is-active', 'is-in', 'is-animated');
    updateNav(i);
    clearProg();

    if (meta.kind === 'video') {
      const v = slide.querySelector('video');
      v.preload = 'auto'; v.load();
      v.play().catch(() => { if (!paused) doNext(); });
      v.onended = () => { if (!paused) doNext(); };
      // Barra de progreso sincronizada con el video
      v.addEventListener('timeupdate', function onTU() {
        if (!paused && v.duration) {
          const pct = (v.currentTime / v.duration) * 100;
          prog.style.transition = 'none';
          prog.style.width = pct + '%';
        }
      });
    } else {
      startProg(meta.duration);
      timer = setTimeout(doNext, meta.duration);
      if (meta.type === 'stats') setTimeout(() => animCounters(slide), 500);
    }
  }

  // ── Transition ───────────────────────────────────────────────
  function doNext() {
    if (paused || trans) return;
    trans = true; clearProg();
    const old = slides[cur];
    const v = old.querySelector('video');
    if (v) { v.pause(); v.currentTime = 0; }
    old.classList.add('is-out'); old.classList.remove('is-in');
    const next = (cur + 1) % TOTAL;
    setTimeout(() => {
      old.classList.remove('is-active','is-out','is-animated');
      cur = next; trans = false; activate(cur);
    }, FADE_MS);
  }

  function jumpTo(i) {
    if (trans) return;
    trans = true; clearProg();
    const old = slides[cur];
    const v = old.querySelector('video');
    if (v) { v.pause(); v.currentTime = 0; }
    old.classList.add('is-out');
    setTimeout(() => {
      old.classList.remove('is-active','is-out','is-animated');
      cur = ((i % TOTAL) + TOTAL) % TOTAL; trans = false; activate(cur);
    }, FADE_MS);
  }

  // ── Pause / resume ───────────────────────────────────────────
  function togglePause() {
    paused = !paused;
    badge.classList.toggle('show', paused);
    const v = slides[cur].querySelector('video');
    if (paused) { clearTimeout(timer); if (v) v.pause(); else pauseProg(); }
    else        { if (v) v.play(); else resumeProg(); }
  }

  // ── Controls ─────────────────────────────────────────────────
  document.addEventListener('keydown', e => {
    hint.classList.add('gone');
    switch (e.key) {
      case 'ArrowRight': case 'ArrowDown':  if (paused) togglePause(); jumpTo(cur+1); break;
      case 'ArrowLeft':  case 'ArrowUp':    if (paused) togglePause(); jumpTo(cur-1); break;
      case ' ':          e.preventDefault(); togglePause(); break;
      case 'f': case 'F':
        document.fullscreenElement ? document.exitFullscreen()
          : document.documentElement.requestFullscreen(); break;
    }
  });

  deck.addEventListener('click', togglePause);

  dots.forEach(d => d.addEventListener('click', ev => {
    ev.stopPropagation();
    if (paused) togglePause();
    jumpTo(+d.dataset.idx);
  }));

  let tx = 0;
  document.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, {passive:true});
  document.addEventListener('touchend',   e => {
    const dx = e.changedTouches[0].clientX - tx;
    if (Math.abs(dx) > 55) { if (paused) togglePause(); jumpTo(dx < 0 ? cur+1 : cur-1); }
  });

  activate(0);
})();
</script>
</body>
</html>
