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
    'accent'  => '#c0392b',   // color base (se sobreescribe por evento)
];

// ════════════════════════════════════════════════════════════════════
//  DIAPOSITIVAS  ← edita aquí el contenido
// ════════════════════════════════════════════════════════════════════
$slides_def = [

    // ── Portada general ─────────────────────────────────────────
    [
        'type'     => 'title',
        'duration' => 9,
        'grad'     => 'linear-gradient(150deg,#0a0a14 0%,#1a0508 100%)',
    ],

    // ── Evento 1: Seminario Ambiental ───────────────────────────
    [
        'type'     => 'event_card',
        'duration' => 11,
        'acc'      => '#1f8c55',
        'grad'     => 'linear-gradient(150deg,#061a0e 0%,#0a2a14 60%,#061410 100%)',
        'committee'=> 'AMIVTAC · COMITÉ DE MEDIO AMBIENTE',
        'number'   => '',
        'bars'     => true,         // dos barras verdes decorativas (como en el sitio)
        'title'    => "SEMINARIO INTERNACIONAL\nDE IMPACTO AMBIENTAL",
        'subtitle' => '"Movilidad en la Selva Maya"',
        'date_main'=> '28-29 Mayo',
        'date_sub' => '2026',
        'loc_main' => 'Campeche',
        'loc_sub'  => 'México · Patrimonio UNESCO',
        'topics'   => [
            'Selva Maya sin Fronteras',
            'Pasos de Fauna',
            'Cooperación Trinacional',
        ],
        'tag'      => 'Temas Centrales',
    ],

    // ── Evento 2: Seminario de Puentes ──────────────────────────
    [
        'type'     => 'event_card',
        'duration' => 11,
        'acc'      => '#00b4a0',
        'grad'     => 'linear-gradient(150deg,#051018 0%,#071e2a 60%,#031410 100%)',
        'committee'=> 'AMIVTAC · COMITÉ TÉCNICO DE PUENTES',
        'number'   => 'VIII',
        'bars'     => false,
        'title'    => "SEMINARIO INTERNACIONAL\nDE PUENTES",
        'subtitle' => '"Una visión para el futuro"',
        'date_main'=> 'Oct. 21-23',
        'date_sub' => '2026',
        'loc_main' => 'Mérida, Yucatán',
        'loc_sub'  => 'Centro Internacional de Congresos',
        'topics'   => [
            'Sostenibilidad y desarrollo responsable',
            'Tecnología e innovación',
            'Expertos internacionales · PIARC',
        ],
        'tag'      => 'Ejes Temáticos',
    ],

    // ── Evento 3: XXV Reunión Nacional ──────────────────────────
    [
        'type'     => 'event_card',
        'duration' => 11,
        'acc'      => '#5cb85c',
        'grad'     => 'linear-gradient(150deg,#0a1205 0%,#1a2a08 60%,#0a1008 100%)',
        'committee'=> 'XXV REUNIÓN NACIONAL DE VÍAS TERRESTRES',
        'number'   => 'XXV',
        'bars'     => false,
        'title'    => "REUNIÓN NACIONAL\nDE VÍAS TERRESTRES",
        'subtitle' => '"Infraestructura que Une y Transforma"',
        'date_main'=> '15-17 Julio',
        'date_sub' => '2026',
        'loc_main' => 'Morelia',
        'loc_sub'  => 'Michoacán, México',
        'topics'   => [
            'Ingeniería Vial de Alto Nivel',
            'Expertos, profesionales y estudiantes',
            'Networking y Expo Vías',
        ],
        'tag'      => 'Destacados',
    ],

    // ── Estadísticas del año ────────────────────────────────────
    [
        'type'     => 'stats',
        'duration' => 9,
        'grad'     => 'linear-gradient(150deg,#0a0a14 0%,#1a0a10 100%)',
        'items'    => [
            ['num' => '3',   'label' => "Eventos\nInternacionales"],
            ['num' => '+20', 'label' => "Ponentes\nEspecialistas"],
            ['num' => '3',   'label' => "Países\nParticipantes"],
        ],
    ],

    // ── Cierre / redes ──────────────────────────────────────────
    [
        'type'     => 'social',
        'duration' => 7,
        'grad'     => 'linear-gradient(150deg,#0a0a14 0%,#1a0508 100%)',
    ],
];

// ── Gradientes de respaldo para diapositivas sin campo 'grad' ──
$grad_defaults = [
    'linear-gradient(140deg,#0d0d1e,#290a0a)',
    'linear-gradient(140deg,#0a1628,#1a0014)',
    'linear-gradient(140deg,#0d1a0d,#1a0a00)',
];

// ── Escanear archivos de media ──────────────────────────────────
function scan_dir_ext(string $dir, array $exts): array {
    if (!is_dir($dir)) return [];
    $out = [];
    foreach (array_diff(scandir($dir), ['.', '..']) as $f)
        if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $exts)) $out[] = $f;
    sort($out);
    return $out;
}

$img_files = scan_dir_ext(__DIR__ . '/assets/images/', ['jpg','jpeg','png','webp','gif']);
$vid_files = scan_dir_ext(__DIR__ . '/assets/videos/', ['mp4','webm','ogg','ogv']);
$img_n     = count($img_files);
$vid_mime  = ['mp4'=>'video/mp4','webm'=>'video/webm','ogg'=>'video/ogg','ogv'=>'video/ogg'];

// Asignar imagen de fondo a cada diapositiva
foreach ($slides_def as $i => &$s) {
    $s['bg']   = $img_n > 0 ? 'assets/images/' . rawurlencode($img_files[$i % $img_n]) : null;
    $s['grad'] = $s['grad'] ?? $grad_defaults[$i % count($grad_defaults)];
}
unset($s);

// Construir playlist intercalando videos
$playlist = [];
$vi = 0; $vc = count($vid_files); $sc = count($slides_def);
$every = $vc > 0 ? max(1, (int)ceil($sc / $vc)) : PHP_INT_MAX;

foreach ($slides_def as $i => $s) {
    $playlist[] = ['kind' => 'content', 's' => $s];
    if ($vc > 0 && $vi < $vc && ($i + 1) % $every === 0) {
        $f = $vid_files[$vi++]; $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        $playlist[] = ['kind'=>'video','src'=>'assets/videos/'.rawurlencode($f),'mime'=>$vid_mime[$ext]??'video/mp4'];
    }
}
while ($vi < $vc) {
    $f = $vid_files[$vi++]; $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    $playlist[] = ['kind'=>'video','src'=>'assets/videos/'.rawurlencode($f),'mime'=>$vid_mime[$ext]??'video/mp4'];
}

$js_playlist = json_encode(array_map(fn($p) => [
    'kind'     => $p['kind'],
    'duration' => $p['kind'] === 'content' ? $p['s']['duration'] * 1000 : null,
    'type'     => $p['kind'] === 'content' ? $p['s']['type'] : 'video',
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
      --acc:  <?= e($cfg['accent']) ?>;
      --ff:   'Montserrat', 'Segoe UI', Arial, sans-serif;
      --fade: 850ms;
    }

    html, body {
      width: 100%; height: 100%;
      overflow: hidden;
      background: #000;
      font-family: var(--ff);
      cursor: none;
    }

    /* ── Deck ────────────────────────────────────────────────────── */
    #deck { position: relative; width: 100vw; height: 100vh; }

    /* ── Slide base ──────────────────────────────────────────────── */
    .slide {
      position: absolute; inset: 0;
      opacity: 0;
      pointer-events: none;
      overflow: hidden;
    }
    .slide.is-active  { opacity: 1; pointer-events: auto; }
    .slide.is-in      { animation: sIn  var(--fade) cubic-bezier(.4,0,.2,1) both; }
    .slide.is-out     { animation: sOut calc(var(--fade) * .85) cubic-bezier(.4,0,.2,1) both; }

    @keyframes sIn  { from { opacity:0; transform:scale(1.025); } to { opacity:1; transform:scale(1); } }
    @keyframes sOut { from { opacity:1; } to { opacity:0; } }

    /* ── Background ──────────────────────────────────────────────── */
    .s-bg {
      position: absolute; inset: -5%;
      background: center/cover no-repeat;
      transform-origin: center;
      will-change: transform;
    }
    .is-animated .s-bg { animation: kenBurns 24s ease-out forwards; }
    @keyframes kenBurns {
      0%   { transform: scale(1.12) translate(1.5%, 1%); }
      100% { transform: scale(1.00) translate(0%, 0%); }
    }

    /* ── Overlay ─────────────────────────────────────────────────── */
    .s-ov {
      position: absolute; inset: 0;
      background: linear-gradient(155deg,
        rgba(0,0,0,.82) 0%,
        rgba(0,0,0,.48) 55%,
        rgba(0,0,0,.70) 100%);
    }

    /* Left accent bar */
    .s-bar {
      position: absolute; left:0; top:8%; bottom:8%;
      width: 6px; background: var(--acc);
      transform: scaleY(0); transform-origin: top;
    }
    .is-animated .s-bar { animation: barIn .55s .1s cubic-bezier(.4,0,.2,1) both; }
    @keyframes barIn { from { transform:scaleY(0); } to { transform:scaleY(1); } }

    /* ── Content wrapper ─────────────────────────────────────────── */
    .s-body {
      position: absolute; inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 5vh 8vw 5vh 10vw;
      z-index: 2;
    }

    /* ── Animated helpers ────────────────────────────────────────── */
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
      border: 1px solid var(--acc);
      border-radius: 50px;
      padding: .4vh 1.4vw;
      font-size: clamp(.5rem,.85vw,1rem);
      font-weight: 700;
      letter-spacing: .25em;
      color: var(--acc);
      text-transform: uppercase;
    }

    /* ═══════════════════════════════════════════════════════════════
       SLIDE: TITLE
    ═══════════════════════════════════════════════════════════════ */
    .t-title {
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 0;
    }
    .t-title .org {
      font-size: clamp(4.5rem,11vw,13rem);
      font-weight: 900;
      letter-spacing: .14em;
      color: #fff;
      line-height: 1;
    }
    .t-title .h-line { width: clamp(5rem,18vw,20rem); margin: 1.8vh auto; }
    .t-title .ev-tag  { margin-bottom: .8vh; }
    .t-title .tagline {
      font-size: clamp(.9rem,1.8vw,2.2rem);
      font-weight: 300;
      color: rgba(255,255,255,.6);
      letter-spacing: .04em;
      margin-top: .5vh;
    }
    .t-title .year-chip {
      margin-top: 2.5vh;
      font-size: clamp(.8rem,1.5vw,1.8rem);
      font-weight: 800;
      letter-spacing: .18em;
      color: var(--acc);
    }

    /* ═══════════════════════════════════════════════════════════════
       SLIDE: EVENT CARD
    ═══════════════════════════════════════════════════════════════ */
    .t-event-card {
      flex-direction: row;
      align-items: center;
      gap: 5vw;
      width: 100%;
    }

    /* Left column */
    .ec-left {
      flex: 1.15;
      display: flex;
      flex-direction: column;
      gap: .9vh;
      position: relative;
    }

    /* Decorative double bars (like screenshot 1) */
    .ec-bars {
      display: flex; gap: .6vw;
      margin-bottom: .5vh;
    }
    .ec-bars span {
      display: block;
      width: clamp(8px,1vw,14px);
      height: clamp(3.5rem,7vh,6rem);
      background: var(--acc);
      border-radius: 3px;
      transform: scaleY(0); transform-origin: bottom;
    }
    .is-animated .ec-bars span:nth-child(1) { animation: barUp .5s .15s cubic-bezier(.4,0,.2,1) both; }
    .is-animated .ec-bars span:nth-child(2) { animation: barUp .5s .3s  cubic-bezier(.4,0,.2,1) both; }
    @keyframes barUp { from{transform:scaleY(0)} to{transform:scaleY(1)} }

    /* Roman numeral */
    .ec-numeral {
      font-size: clamp(4rem,9vw,12rem);
      font-weight: 900;
      color: var(--acc);
      line-height: .9;
      letter-spacing: .04em;
    }

    .ec-committee {
      font-size: clamp(.5rem,.85vw,1rem);
      font-weight: 700;
      letter-spacing: .2em;
      color: var(--acc);
      text-transform: uppercase;
    }

    .ec-title {
      font-size: clamp(1.6rem,3.8vw,4.8rem);
      font-weight: 900;
      letter-spacing: .04em;
      color: #fff;
      line-height: 1.08;
      white-space: pre-line;
    }

    .ec-subtitle {
      font-size: clamp(.8rem,1.5vw,1.8rem);
      font-weight: 300;
      font-style: italic;
      color: rgba(255,255,255,.7);
      margin-top: -.2vh;
    }

    /* Date + location chips */
    .ec-chips {
      display: flex;
      gap: 1vw;
      margin-top: .8vh;
      flex-wrap: wrap;
    }
    .ec-chip {
      display: flex;
      align-items: center;
      gap: .8vw;
      background: rgba(0,0,0,.4);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 10px;
      padding: .7vh 1.2vw;
    }
    .chip-icon { font-size: clamp(.9rem,1.6vw,2rem); line-height: 1; }
    .chip-main {
      font-size: clamp(.7rem,1.1vw,1.3rem);
      font-weight: 700;
      color: #fff;
      line-height: 1.2;
    }
    .chip-sub {
      font-size: clamp(.55rem,.85vw,1rem);
      color: rgba(255,255,255,.5);
      line-height: 1.2;
    }

    /* Right column — topics card */
    .ec-right {
      flex: .85;
      display: flex;
      flex-direction: column;
      gap: 1.2vh;
      background: rgba(0,0,0,.45);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1px solid rgba(255,255,255,.1);
      border-left: 3px solid var(--acc);
      border-radius: 16px;
      padding: 3vh 2.5vw;
    }

    .topics-heading {
      font-size: clamp(.55rem,.9vw,1.05rem);
      font-weight: 700;
      letter-spacing: .25em;
      color: var(--acc);
      text-transform: uppercase;
      margin-bottom: .4vh;
    }

    .ec-topic {
      display: flex;
      align-items: flex-start;
      gap: .8vw;
      font-size: clamp(.7rem,1.2vw,1.4rem);
      font-weight: 400;
      color: rgba(255,255,255,.75);
      line-height: 1.4;
    }
    .ec-topic::before {
      content: '';
      display: block;
      width: 7px; height: 7px;
      border-radius: 50%;
      background: var(--acc);
      flex-shrink: 0;
      margin-top: .45em;
    }

    /* ═══════════════════════════════════════════════════════════════
       SLIDE: STATS
    ═══════════════════════════════════════════════════════════════ */
    .t-stats {
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 3vh;
    }
    .t-stats .s-label {
      font-size: clamp(.6rem,1vw,1.2rem);
      font-weight: 700;
      letter-spacing: .3em;
      color: var(--acc);
    }
    .stats-row { display: flex; gap: 6vw; justify-content: center; }
    .stat-box  { display: flex; flex-direction: column; align-items: center; gap: .6vh; }
    .stat-num  {
      font-size: clamp(3.5rem,8.5vw,11rem);
      font-weight: 900;
      color: #fff;
      line-height: 1;
      font-variant-numeric: tabular-nums;
    }
    .stat-sep  { height: 3px; width: 2.5rem; background: var(--acc); border-radius: 2px; }
    .stat-lbl  {
      font-size: clamp(.65rem,1.1vw,1.3rem);
      font-weight: 400;
      color: rgba(255,255,255,.55);
      white-space: pre-line;
      text-align: center;
      line-height: 1.5;
    }

    /* ═══════════════════════════════════════════════════════════════
       SLIDE: SOCIAL
    ═══════════════════════════════════════════════════════════════ */
    .t-social {
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 2vh;
    }
    .t-social .follow {
      font-size: clamp(.6rem,1vw,1.2rem);
      font-weight: 600;
      letter-spacing: .3em;
      color: rgba(255,255,255,.5);
    }
    .t-social .hashtag {
      font-size: clamp(2.5rem,6.5vw,8.5rem);
      font-weight: 900;
      letter-spacing: .04em;
      color: var(--acc);
    }
    .t-social .h-line { width: clamp(3rem,9vw,11rem); margin: .4vh auto; }
    .t-social .web {
      font-size: clamp(.8rem,1.5vw,1.8rem);
      font-weight: 300;
      color: rgba(255,255,255,.55);
      letter-spacing: .08em;
    }

    /* ── Video slide ──────────────────────────────────────────────── */
    .s-video { position: absolute; inset:0; width:100%; height:100%; object-fit:cover; }

    /* ── Progress bar ────────────────────────────────────────────── */
    #prog {
      position: fixed; top:0; left:0;
      height: 3px; width: 0%;
      background: var(--acc);
      z-index: 600;
    }

    /* ── Nav dots / counter ──────────────────────────────────────── */
    #nav {
      position: fixed; bottom:20px; left:50%;
      transform: translateX(-50%);
      display: flex; align-items: center; gap: 9px;
      z-index: 600;
    }
    .dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: rgba(255,255,255,.28);
      transition: background .3s, transform .3s;
    }
    .dot.on { background: var(--acc); transform: scale(1.45); }
    #counter {
      color: rgba(255,255,255,.45);
      font-family: var(--ff);
      font-size: .78rem;
      letter-spacing: .08em;
      font-variant-numeric: tabular-nums;
    }

    /* ── Pause badge ─────────────────────────────────────────────── */
    #paused {
      position: fixed; top:24px; right:28px;
      background: rgba(0,0,0,.6);
      border: 1px solid rgba(255,255,255,.18);
      border-radius: 8px;
      padding: 8px 20px;
      color: #fff; font-family: var(--ff);
      font-size: .82rem; letter-spacing: .12em;
      opacity: 0; transition: opacity .25s;
      pointer-events: none; z-index: 600;
    }
    #paused.show { opacity: 1; }

    /* ── Keyboard hint ───────────────────────────────────────────── */
    #hint {
      position: fixed; bottom: 48px; left: 50%;
      transform: translateX(-50%);
      background: rgba(0,0,0,.5); border-radius: 6px;
      padding: 5px 14px;
      color: rgba(255,255,255,.45); font-family: var(--ff);
      font-size: .68rem; letter-spacing: .07em;
      opacity: 1; transition: opacity 1.2s;
      pointer-events: none; z-index: 600;
    }
    #hint.gone { opacity: 0; }
  </style>
</head>
<body>

<div id="deck">
<?php foreach ($playlist as $pi => $p): ?>

<?php if ($p['kind'] === 'video'): ?>

  <div class="slide" data-kind="video" data-idx="<?= $pi ?>">
    <video class="s-video" muted playsinline preload="<?= $pi === 0 ? 'auto' : 'none' ?>">
      <source src="<?= e($p['src']) ?>" type="<?= e($p['mime']) ?>">
    </video>
  </div>

<?php else: $s = $p['s']; ?>

  <div class="slide"
       data-kind="content"
       data-type="<?= e($s['type']) ?>"
       data-duration="<?= (int)($s['duration'] * 1000) ?>"
       data-idx="<?= $pi ?>"
       style="--acc:<?= e($s['acc'] ?? $cfg['accent']) ?>">

    <div class="s-bg" style="background-image:<?=
      $s['bg'] ? "url('" . e($s['bg']) . "')" : $s['grad']
    ?>"></div>
    <div class="s-ov"></div>
    <div class="s-bar"></div>

    <?php /* ── TITLE ────────────────────────────────────────────── */ ?>
    <?php if ($s['type'] === 'title'): ?>
    <div class="s-body t-title">
      <div class="label-tag ev-tag a" style="--d:.1s"><?= e($cfg['org']) ?> · Eventos <?= e($cfg['year']) ?></div>
      <div class="org a" style="--d:.3s"><?= e($cfg['org']) ?></div>
      <div class="h-line a" style="--d:.7s"></div>
      <div class="tagline a" style="--d:.9s"><?= e($cfg['tagline']) ?></div>
      <div class="year-chip a" style="--d:1.1s"><?= e($cfg['year']) ?></div>
    </div>

    <?php /* ── EVENT CARD ──────────────────────────────────────── */ ?>
    <?php elseif ($s['type'] === 'event_card'): ?>
    <div class="s-body t-event-card">

      <!-- Left column -->
      <div class="ec-left">

        <?php if (!empty($s['bars'])): ?>
        <div class="ec-bars"><span></span><span></span></div>
        <?php endif; ?>

        <div class="ec-committee a" style="--d:.1s"><?= e($s['committee']) ?></div>

        <?php if ($s['number']): ?>
        <div class="ec-numeral a" style="--d:.25s"><?= e($s['number']) ?></div>
        <?php endif; ?>

        <div class="ec-title a" style="--d:.<?= $s['number'] ? '4' : '3' ?>s"><?= e($s['title']) ?></div>
        <div class="ec-subtitle a" style="--d:.<?= $s['number'] ? '6' : '5' ?>s"><?= e($s['subtitle']) ?></div>

        <div class="ec-chips a" style="--d:.7s">
          <div class="ec-chip">
            <span class="chip-icon">📅</span>
            <div>
              <div class="chip-main"><?= e($s['date_main']) ?></div>
              <div class="chip-sub"><?= e($s['date_sub']) ?></div>
            </div>
          </div>
          <div class="ec-chip">
            <span class="chip-icon">📍</span>
            <div>
              <div class="chip-main"><?= e($s['loc_main']) ?></div>
              <div class="chip-sub"><?= e($s['loc_sub']) ?></div>
            </div>
          </div>
        </div>

      </div>

      <!-- Right column -->
      <div class="ec-right a-fade a" style="--d:.5s">
        <div class="topics-heading"><?= e($s['tag']) ?></div>
        <?php foreach ($s['topics'] as $topic): ?>
        <div class="ec-topic"><?= e($topic) ?></div>
        <?php endforeach; ?>
      </div>

    </div>

    <?php /* ── STATS ───────────────────────────────────────────── */ ?>
    <?php elseif ($s['type'] === 'stats'): ?>
    <div class="s-body t-stats">
      <div class="s-label a" style="--d:.1s"><?= e($cfg['org']) ?> · <?= e($cfg['year']) ?></div>
      <div class="stats-row">
        <?php foreach ($s['items'] as $si => $st): $d = round(.3 + $si * .22, 2); ?>
        <div class="stat-box a" style="--d:<?= $d ?>s">
          <div class="stat-num" data-target="<?= e($st['num']) ?>">0</div>
          <div class="stat-sep"></div>
          <div class="stat-lbl"><?= e($st['label']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php /* ── SOCIAL ──────────────────────────────────────────── */ ?>
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
    <?php foreach ($playlist as $pi => $_): ?><div class="dot" data-idx="<?= $pi ?>"></div><?php endforeach; ?>
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

  // ── Progress bar ──────────────────────────────────────────────
  function startProg(ms) {
    progDur = ms; progStart = performance.now();
    prog.style.transition = 'none';
    prog.style.width = '0%';
    requestAnimationFrame(() => {
      prog.style.transition = `width ${ms}ms linear`;
      prog.style.width = '100%';
    });
  }
  function pauseProg() {
    clearTimeout(timer);
    const pct = Math.min(100, ((performance.now() - progStart) / progDur) * 100);
    prog.style.transition = 'none';
    prog.style.width = pct + '%';
  }
  function resumeProg() {
    const rem = Math.max(300, progDur - (performance.now() - progStart));
    prog.style.transition = `width ${rem}ms linear`;
    prog.style.width = '100%';
    timer = setTimeout(doNext, rem);
  }
  function clearProg() {
    clearTimeout(timer);
    prog.style.transition = 'none';
    prog.style.width = '0%';
  }

  // ── Nav ───────────────────────────────────────────────────────
  function updateNav(i) {
    if (USE_DOTS) dots.forEach(d => d.classList.toggle('on', +d.dataset.idx === i));
    else if (ctr) ctr.textContent = `${i + 1} / ${TOTAL}`;
  }

  // ── Counter animation ─────────────────────────────────────────
  function animCounters(el) {
    el.querySelectorAll('.stat-num[data-target]').forEach(n => {
      const raw = n.dataset.target;
      const pre = raw.startsWith('+') ? '+' : '';
      const max = parseInt(raw.replace(/\D/g, ''), 10);
      const t0  = performance.now();
      const dur = 1800;
      (function tick(now) {
        const p = Math.min((now - t0) / dur, 1);
        n.textContent = pre + Math.round(max * (1 - Math.pow(1 - p, 3)));
        if (p < 1) requestAnimationFrame(tick);
      })(t0);
    });
  }

  // ── Activate slide ────────────────────────────────────────────
  function activate(i) {
    const slide = slides[i];
    const meta  = PLAYLIST[i];

    // Reset so animations replay on loop
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
    } else {
      startProg(meta.duration);
      timer = setTimeout(doNext, meta.duration);
      if (meta.type === 'stats') setTimeout(() => animCounters(slide), 500);
    }
  }

  // ── Transition ────────────────────────────────────────────────
  function doNext() {
    if (paused || trans) return;
    trans = true; clearProg();
    const old = slides[cur];
    const vid = old.querySelector('video');
    if (vid) { vid.pause(); vid.currentTime = 0; }
    old.classList.add('is-out'); old.classList.remove('is-in');
    const next = (cur + 1) % TOTAL;
    setTimeout(() => {
      old.classList.remove('is-active', 'is-out', 'is-animated');
      cur = next; trans = false; activate(cur);
    }, FADE_MS);
  }

  function jumpTo(i) {
    if (trans) return;
    trans = true; clearProg();
    const old = slides[cur];
    const vid = old.querySelector('video');
    if (vid) { vid.pause(); vid.currentTime = 0; }
    old.classList.add('is-out');
    setTimeout(() => {
      old.classList.remove('is-active', 'is-out', 'is-animated');
      cur = ((i % TOTAL) + TOTAL) % TOTAL; trans = false; activate(cur);
    }, FADE_MS);
  }

  // ── Pause / resume ────────────────────────────────────────────
  function togglePause() {
    paused = !paused;
    badge.classList.toggle('show', paused);
    const vid = slides[cur].querySelector('video');
    if (paused) { clearTimeout(timer); if (vid) vid.pause(); else pauseProg(); }
    else        { if (vid) vid.play(); else resumeProg(); }
  }

  // ── Events ───────────────────────────────────────────────────
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
