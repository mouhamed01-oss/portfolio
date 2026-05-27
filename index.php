<?php
require_once 'config.php';
$pdo = db();

$diplomas    = $pdo->query("SELECT * FROM diplomas    ORDER BY sort_order ASC")->fetchAll();
$experiences = $pdo->query("SELECT * FROM experiences ORDER BY sort_order ASC")->fetchAll();
$projects    = $pdo->query("SELECT * FROM projects    ORDER BY is_featured DESC, sort_order ASC")->fetchAll();

// Traitement formulaire contact
$formMsg = $formErr = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$subject || !$message) {
        $formErr = 'Veuillez remplir tous les champs correctement.';
    } else {
        $pdo->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)")
            ->execute([$name, $email, $subject, $message]);
        // Optionnel : mail(SITE_EMAIL, "Portfolio — $subject", $message, "From: $email");
        $formMsg = 'Message envoyé avec succès ! Je vous réponds sous 24h.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mouhamed Diallo — Développeur & Designer</title>
<meta name="description" content="Portfolio de Mouhamed Diallo, Ingénieur Informatique polyvalent spécialisé en développement web, logiciel et design graphique à Dakar.">

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:ital,wght@0,300;0,400;1,300&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

<!-- AOS -->
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

<script>
// Tailwind config
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        display: ['Syne', 'sans-serif'],
        serif:   ['Instrument Serif', 'Georgia', 'serif'],
        mono:    ['DM Mono', 'monospace'],
      },
      colors: {
        ink:   { DEFAULT:'#0a0a0a', 50:'#f5f5f5', 100:'#e8e8e8', 200:'#d0d0d0', 800:'#1a1a1a', 900:'#0d0d0d' },
        acid:  { DEFAULT:'#c8f135', dim:'rgba(200,241,53,0.12)', border:'rgba(200,241,53,0.3)' },
        chalk: { DEFAULT:'#f0ede6', dim:'rgba(240,237,230,0.06)' },
      },
      animation: {
        'spin-slow':  'spin 12s linear infinite',
        'float':      'float 6s ease-in-out infinite',
        'blink':      'blink 1s step-end infinite',
        'slide-up':   'slideUp 0.7s cubic-bezier(0.16,1,0.3,1) both',
        'fade-in':    'fadeIn 0.6s ease both',
        'gradient':   'gradientShift 8s ease infinite',
      },
      keyframes: {
        float:         { '0%,100%':{ transform:'translateY(0)' }, '50%':{ transform:'translateY(-14px)' } },
        blink:         { '0%,100%':{ opacity:1 }, '50%':{ opacity:0 } },
        slideUp:       { from:{ opacity:0, transform:'translateY(50px)' }, to:{ opacity:1, transform:'translateY(0)' } },
        fadeIn:        { from:{ opacity:0 }, to:{ opacity:1 } },
        gradientShift: { '0%,100%':{ backgroundPosition:'0% 50%' }, '50%':{ backgroundPosition:'100% 50%' } },
      },
    }
  }
}
</script>

<style>
  :root {
    --bg:       #0a0a0a;
    --bg2:      #111111;
    --bg3:      #181818;
    --border:   rgba(255,255,255,0.07);
    --text:     #f0ede6;
    --text2:    #888880;
    --accent:   #c8f135;
    --accent2:  rgba(200,241,53,0.12);
  }
  [data-theme="light"] {
    --bg:       #f5f4f0;
    --bg2:      #eceae3;
    --bg3:      #ffffff;
    --border:   rgba(0,0,0,0.08);
    --text:     #0a0a0a;
    --text2:    #666660;
    --accent:   #4a7c00;
    --accent2:  rgba(74,124,0,0.1);
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  html { scroll-behavior: smooth; }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'DM Mono', monospace;
    font-weight: 300;
    overflow-x: hidden;
    transition: background 0.4s ease, color 0.3s ease;
  }

  /* ---- CURSEUR ---- */
  .cursor      { position:fixed; width:10px; height:10px; background:var(--accent); border-radius:50%; pointer-events:none; z-index:9999; transform:translate(-50%,-50%); transition:transform 0.15s ease; mix-blend-mode:difference; }
  .cursor-ring { position:fixed; width:36px; height:36px; border:1px solid var(--accent); border-radius:50%; pointer-events:none; z-index:9998; transform:translate(-50%,-50%); transition:width 0.3s,height 0.3s,left 0.08s,top 0.08s; }
  body:hover .cursor { opacity:1; }

  /* ---- NOISE TEXTURE ---- */
  body::before {
    content:''; position:fixed; inset:0; pointer-events:none; z-index:0; opacity:0.025;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
    background-size: 128px 128px;
  }

  /* ---- NAVBAR ---- */
  nav { background: rgba(10,10,10,0.8); backdrop-filter: blur(20px); border-bottom:1px solid var(--border); }
  [data-theme="light"] nav { background: rgba(245,244,240,0.85); }

  /* ---- HERO ---- */
  .hero-number { font-family:'Syne',sans-serif; font-size:clamp(100px,18vw,240px); font-weight:800; line-height:0.85; color:transparent; -webkit-text-stroke:1px rgba(200,241,53,0.15); user-select:none; pointer-events:none; }
  [data-theme="light"] .hero-number { -webkit-text-stroke:1px rgba(74,124,0,0.12); }
  .hero-title  { font-family:'Syne',sans-serif; font-size:clamp(2.5rem,7vw,6rem); font-weight:800; line-height:1; }
  .hero-sub    { font-family:'Instrument Serif',serif; font-style:italic; font-size:clamp(1.1rem,2.5vw,1.6rem); color:var(--text2); }
  .typed-cursor { animation: blink 1s step-end infinite; }

  /* ---- ACCENT LINE ---- */
  .accent-line { display:inline-block; width:40px; height:2px; background:var(--accent); vertical-align:middle; }

  /* ---- SECTION TITLE ---- */
  .section-num { font-family:'DM Mono',monospace; font-size:0.7rem; color:var(--accent); letter-spacing:0.2em; }
  .section-title { font-family:'Syne',sans-serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:800; }

  /* ---- CARDS ---- */
  .card {
    background:var(--bg2); border:1px solid var(--border);
    border-radius:2px; padding:2rem;
    transition:all 0.35s cubic-bezier(0.16,1,0.3,1);
    position:relative; overflow:hidden;
  }
  .card::before {
    content:''; position:absolute; inset:0; background:var(--accent2);
    opacity:0; transition:opacity 0.3s ease;
  }
  .card:hover { border-color:var(--accent); transform:translateY(-4px); }
  .card:hover::before { opacity:1; }

  /* ---- SKILL BAR ---- */
  .skill-track { height:2px; background:var(--border); border-radius:2px; overflow:hidden; }
  .skill-fill  { height:100%; background:var(--accent); border-radius:2px; width:0; transition:width 1.2s cubic-bezier(0.16,1,0.3,1); }

  /* ---- TAG ---- */
  .tag { display:inline-block; font-size:0.65rem; letter-spacing:0.12em; text-transform:uppercase; padding:3px 10px; border:1px solid var(--border); border-radius:1px; color:var(--text2); transition:all 0.2s; }
  .tag:hover,.tag.accent { border-color:var(--accent); color:var(--accent); }

  /* ---- TIMELINE ---- */
  .tl-line { position:absolute; left:0; top:0; bottom:0; width:1px; background:var(--border); }
  .tl-dot  { position:absolute; left:-5px; top:6px; width:10px; height:10px; border-radius:50%; background:var(--bg); border:1px solid var(--accent); }

  /* ---- FORM ---- */
  .field { background:var(--bg2); border:1px solid var(--border); border-radius:2px; padding:12px 16px; width:100%; font-family:'DM Mono',monospace; font-size:0.85rem; color:var(--text); outline:none; transition:border-color 0.2s; }
  .field:focus { border-color:var(--accent); }
  .field::placeholder { color:var(--text2); }
  [data-theme="light"] .field { background:var(--bg3); }

  /* ---- BTN ---- */
  .btn-acid { background:var(--accent); color:#0a0a0a; font-family:'Syne',sans-serif; font-weight:700; font-size:0.8rem; letter-spacing:0.1em; text-transform:uppercase; padding:14px 32px; border:none; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:8px; }
  .btn-acid:hover { filter:brightness(1.1); transform:translateY(-2px); }
  .btn-outline { background:transparent; color:var(--text); border:1px solid var(--border); font-family:'Syne',sans-serif; font-weight:600; font-size:0.8rem; letter-spacing:0.08em; text-transform:uppercase; padding:12px 28px; cursor:pointer; transition:all 0.25s; display:inline-flex; align-items:center; gap:8px; }
  .btn-outline:hover { border-color:var(--accent); color:var(--accent); }

  /* ---- THEME TOGGLE ---- */
  .theme-toggle { position:relative; width:52px; height:28px; background:var(--bg3); border:1px solid var(--border); border-radius:14px; cursor:pointer; transition:background 0.3s; }
  .theme-toggle::after { content:''; position:absolute; top:3px; left:4px; width:20px; height:20px; border-radius:50%; background:var(--accent); transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1); }
  [data-theme="light"] .theme-toggle::after { transform:translateX(22px); }

  /* ---- PROJECT CARD ---- */
  .proj-cat { font-size:0.65rem; text-transform:uppercase; letter-spacing:0.2em; }
  .proj-img { aspect-ratio:16/9; background:var(--bg3); border-bottom:1px solid var(--border); overflow:hidden; position:relative; }
  .proj-img-placeholder { display:flex; align-items:center; justify-content:center; height:100%; font-size:3rem; opacity:0.3; }

  /* ---- ROTATING BADGE ---- */
  .rotate-badge { animation: spin-slow 12s linear infinite; }

  /* ---- SCROLL INDICATOR ---- */
  .scroll-progress { position:fixed; top:0; left:0; height:2px; background:var(--accent); z-index:9999; width:0%; transition:width 0.1s; }

  /* ---- MOBILE NAV ---- */
  .mobile-menu { display:none; }
  .mobile-menu.open { display:flex; }

  @media(max-width:768px) {
    .cursor,.cursor-ring { display:none; }
    .hero-number { font-size:clamp(70px,20vw,130px); }
  }

  /* ---- AOS custom ---- */
  [data-aos="slide-left"]  { transform:translateX(-60px); opacity:0; transition-property:transform,opacity; }
  [data-aos="slide-left"].aos-animate { transform:translateX(0); opacity:1; }
</style>
</head>
<body>

<!-- SCROLL PROGRESS -->
<div class="scroll-progress" id="scrollBar"></div>

<!-- CURSEUR CUSTOM -->
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- ================================================================
     NAVIGATION
     ================================================================ -->
<nav class="fixed top-0 left-0 right-0 z-50">
  <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
    <!-- Logo -->
    <a href="index.php" class="font-display font-bold text-lg tracking-tight flex items-center gap-2" style="color:var(--text)">
      <span style="color:var(--accent)">MD</span>
      <span class="hidden sm:block" style="color:var(--text2); font-size:0.7rem; font-family:'DM Mono',monospace; letter-spacing:0.1em">/ PORTFOLIO</span>
    </a>

    <!-- Links desktop -->
    <div class="hidden md:flex items-center gap-8">
      <?php foreach([['#about','À PROPOS'],['#skills','COMPÉTENCES'],['#projects','PROJETS'],['#resume','PARCOURS'],['#contact','CONTACT']] as [$href,$label]): ?>
      <a href="<?= $href ?>" style="font-size:0.72rem; letter-spacing:0.15em; color:var(--text2); font-family:'DM Mono',monospace;" class="hover:text-white transition-colors duration-200"><?= $label ?></a>
      <?php endforeach; ?>
    </div>

    <!-- Right controls -->
    <div class="flex items-center gap-4">
      <!-- Theme toggle -->
      <button class="theme-toggle" id="themeToggle" aria-label="Changer le thème" title="Mode clair / sombre"></button>

      <!-- Admin link -->
      <a href="admin/index.php" style="font-size:0.65rem; letter-spacing:0.15em; color:var(--text2);" class="hidden md:block font-mono hover:text-white transition-colors">
        [ADMIN]
      </a>

      <!-- Hamburger -->
      <button id="hamburger" class="md:hidden flex flex-col gap-1.5 p-1" aria-label="Menu">
        <span class="block w-6 h-px transition-all duration-300" style="background:var(--text)"></span>
        <span class="block w-4 h-px transition-all duration-300" style="background:var(--text)"></span>
        <span class="block w-6 h-px transition-all duration-300" style="background:var(--text)"></span>
      </button>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="mobileMenu" class="mobile-menu flex-col px-6 pb-6 gap-4 md:hidden border-t" style="border-color:var(--border); background:var(--bg)">
    <?php foreach([['#about','À PROPOS'],['#skills','COMPÉTENCES'],['#projects','PROJETS'],['#resume','PARCOURS'],['#contact','CONTACT']] as [$href,$label]): ?>
    <a href="<?= $href ?>" style="font-size:0.8rem; letter-spacing:0.15em; color:var(--text2);" class="font-mono"><?= $label ?></a>
    <?php endforeach; ?>
    <a href="admin/index.php" style="font-size:0.8rem; letter-spacing:0.15em; color:var(--accent);" class="font-mono">[ADMIN]</a>
  </div>
</nav>


<!-- ================================================================
     HERO
     ================================================================ -->
<section class="relative min-h-screen flex flex-col justify-center overflow-hidden pt-16">

  <!-- Numéro décoratif -->
  <div class="hero-number absolute right-0 top-1/2 -translate-y-1/2 select-none pointer-events-none leading-none pr-0" style="right:-2vw; opacity:0.35">01</div>

  <!-- Badge rotatif -->
  <div class="absolute top-24 right-8 md:right-16 w-24 h-24 md:w-32 md:h-32 hidden sm:flex items-center justify-center">
    <svg class="rotate-badge w-full h-full" viewBox="0 0 120 120">
      <defs><path id="circle" d="M60,60 m-40,0 a40,40 0 1,1 80,0 a40,40 0 1,1 -80,0"/></defs>
      <text font-size="10.5" fill="currentColor" style="fill:var(--text2);font-family:'DM Mono',monospace;letter-spacing:3px">
        <textPath href="#circle">DÉVELOPPEUR · DESIGNER · CRÉATEUR ·</textPath>
      </text>
    </svg>
    <div class="absolute text-2xl" style="color:var(--accent)">✦</div>
  </div>

  <div class="max-w-7xl mx-auto px-6 w-full relative z-10">
    <!-- Eyebrow -->
    <div class="flex items-center gap-3 mb-8 animate-slide-up">
      <span class="accent-line"></span>
      <span style="font-size:0.7rem; letter-spacing:0.25em; color:var(--accent); font-family:'DM Mono',monospace;">MOUHAMED DIALLO — GÉNIE INFORMATIQUE</span>
    </div>

    <!-- Titre principal -->
    <h1 class="hero-title mb-6 animate-slide-up" style="animation-delay:0.1s">
      Créer.
      <br>
      <span class="font-serif italic" style="color:var(--text2); font-size:0.9em">Concevoir.</span>
      <br>
      <span style="color:var(--accent)">Innover<span class="typed-cursor">_</span></span>
    </h1>

    <p class="hero-sub mb-10 max-w-xl animate-slide-up" style="animation-delay:0.2s">
      Ingénieur polyvalent à la croisée du <em>code</em> et du <em>design</em> —<br>
      de WINDEV à Photoshop, du pixel à la ligne de commande.
    </p>

    <div class="flex flex-wrap gap-4 animate-slide-up" style="animation-delay:0.3s">
      <a href="#projects" class="btn-acid">Voir mes projets →</a>
      <a href="#contact" class="btn-outline">Me contacter</a>
    </div>

    <!-- Stats -->
    <div class="flex gap-8 mt-16 animate-slide-up" style="animation-delay:0.4s">
      <?php foreach([['3+','Ans d\'XP'],['20+','Projets livrés'],['4','Domaines d\'expertise']] as [$n,$l]): ?>
      <div>
        <div class="font-display font-bold" style="font-size:2rem; color:var(--accent)"><?= $n ?></div>
        <div style="font-size:0.65rem; letter-spacing:0.12em; color:var(--text2); font-family:'DM Mono',monospace"><?= $l ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Ligne de fond -->
  <div class="absolute bottom-0 left-0 right-0 h-px" style="background:var(--border)"></div>
  <!-- Scroll hint -->
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-float">
    <span style="font-size:0.6rem; letter-spacing:0.2em; color:var(--text2); font-family:'DM Mono',monospace">SCROLL</span>
    <div style="width:1px; height:40px; background:linear-gradient(to bottom, var(--accent), transparent)"></div>
  </div>
</section>


<!-- ================================================================
     À PROPOS
     ================================================================ -->
<section id="about" class="py-32" style="background:var(--bg2); border-top:1px solid var(--border)">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid md:grid-cols-2 gap-16 items-center">

      <!-- Photo placeholder -->
      <div data-aos="fade-right" data-aos-duration="900" class="relative">
        <div class="relative w-full max-w-sm mx-auto" style="aspect-ratio:3/4">
          <!-- Cadre décoratif -->
          <div class="absolute -top-4 -left-4 w-full h-full border" style="border-color:var(--accent); opacity:0.3; border-radius:2px"></div>
          <!-- Photo -->
          <div class="w-full h-full flex flex-col items-center justify-center" style="background:var(--bg3); border:1px solid var(--border); border-radius:2px">
            <img src="pi.jpg" alt="" srcset="">
            <svg width="80" height="80" viewBox="0 0 80 80" fill="none" style="opacity:0.2">
              <circle cx="40" cy="30" r="18" stroke="currentColor" stroke-width="1.5"/>
              <path d="M8 72c0-17.673 14.327-32 32-32s32 14.327 32 32" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <!-- <span style="font-size:0.65rem; letter-spacing:0.2em; color:var(--text2); margin-top:12px; font-family:'DM Mono',monospace">VOTRE PHOTO ICI</span> -->
          </div>
          <!-- Badge accent -->
          <div class="absolute -bottom-4 -right-4 px-4 py-2" style="background:var(--accent); color:#0a0a0a">
            <span style="font-family:'Syne',sans-serif; font-weight:800; font-size:0.75rem;">Ouvert à travailler</span>
          </div>
        </div>
      </div>

      <!-- Texte -->
      <div data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
        <div class="flex items-center gap-3 mb-4">
          <span class="section-num">// 01</span>
          <span class="accent-line"></span>
        </div>
        <h2 class="section-title mb-6">À Propos<br><span class="font-serif italic" style="color:var(--text2)">de moi.</span></h2>

        <div style="color:var(--text2); line-height:1.8; font-size:0.9rem;" class="space-y-4">
          <p>Diplômé en <strong style="color:var(--text)">Licence Génie Informatique</strong>, je navigue avec aisance entre deux univers qui se complètent : la rigueur du code et la sensibilité du design.</p>
          <p>Depuis plus de <strong style="color:var(--accent)">3 ans</strong>, je conçois des solutions qui répondent aux besoins réels des utilisateurs — des logiciels de gestion sur mesure (WINDEV) aux sites e-commerce, en passant par des identités visuelles mémorables.</p>
          <p>Ma philosophie : <em style="color:var(--text); font-family:'Instrument Serif',serif; font-size:1.05em">chaque projet mérite un regard à la fois technique et esthétique.</em></p>
        </div>

        <div class="flex flex-wrap gap-2 mt-8">
          <?php foreach(['Dakar, Sénégal','Disponible en freelance','Bilingue FR/EN'] as $info): ?>
          <span class="tag"><?= $info ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ================================================================
     COMPÉTENCES
     ================================================================ -->
<section id="skills" class="py-32" style="border-top:1px solid var(--border)">
  <div class="max-w-7xl mx-auto px-6">
    <div class="mb-16" data-aos="fade-up">
      <span class="section-num">// 02</span>
      <h2 class="section-title mt-2">Compétences<br><span class="font-serif italic" style="color:var(--text2)">& Outils.</span></h2>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-px" style="background:var(--border)">
      <?php
      $skillGroups = [
        ['icon'=>'🎨','label'=>'Design Graphique','color'=>'#ff6b6b','skills'=>[
          ['Photoshop',92],['Illustrator',88],['After Effects',80],['Premiere Pro',78],['Cinema 4D',65],['InDesign',85],['Canva',95],
        ]],
        ['icon'=>'💻','label'=>'Développement Web','color'=>'#4ecdc4','skills'=>[
          ['PHP/MySQL',85],['HTML5/CSS3',90],['JavaScript',75],['Python',85],['IA',80],['E-Commerce',78],['CMS',70],
        ]],
        ['icon'=>'💻','label'=>'Programmation: Creation de Logiciel DESKTOK, WEB','color'=>'#c8f135','skills'=>[
          ['WINDEV',88],['WEBDEV',80],['WLangage',85],['HyperFile SQL',78],['Gestion Comm.',90],['Gestion École',85],
        ]],
         ['icon'=>'🛠','label'=>'Maintenance Informatique et Electronique','color'=>'#f1af35','skills'=>[
          ['Diagnostique',88],['Reparation ordinateurs pc/mac',80],['Suppression virus et Protection virus',85],['Nettoyage et optimisation pc/mac',90],['Installation logiciels',85],['Installation systeme d-exploitation',85]
        ]],
        ['icon'=>'📊','label'=>'Bureautique','color'=>'#a29bfe','skills'=>[
          ['Excel Avancé',92],['VBA/Macros',82],['Word',95],['Access',80],['PowerPoint',88],
        ]],
      ];
      foreach($skillGroups as $group): ?>
      <div class="card" style="border-radius:0; border:none;" data-aos="fade-up" data-aos-delay="<?= array_search($group,$skillGroups)*100 ?>">
        <div class="text-2xl mb-3"><?= $group['icon'] ?></div>
        <h3 class="font-display font-bold mb-1" style="font-size:0.85rem; letter-spacing:0.05em; color:var(--text)"><?= $group['label'] ?></h3>
        <div class="w-8 h-0.5 mb-6" style="background:<?= $group['color'] ?>"></div>

        <div class="space-y-4">
          <?php foreach($group['skills'] as [$name,$pct]): ?>
          <div>
            <div class="flex justify-between mb-1">
              <span style="font-size:0.72rem; color:var(--text2)"><?= $name ?></span>
              <span style="font-size:0.65rem; color:<?= $group['color'] ?>; font-family:'DM Mono',monospace"><?= $pct ?>%</span>
            </div>
            <div class="skill-track">
              <div class="skill-fill" data-width="<?= $pct ?>" style="background:<?= $group['color'] ?>"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ================================================================
     PROJETS
     ================================================================ -->
<section id="projects" class="py-32" style="background:var(--bg2); border-top:1px solid var(--border)">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
      <div data-aos="fade-up">
        <span class="section-num">// 03</span>
        <h2 class="section-title mt-2">Projets<br><span class="font-serif italic" style="color:var(--text2)">réalisés.</span></h2>
      </div>
      <!-- Filtres -->
      <div class="flex gap-2 flex-wrap" data-aos="fade-up" data-aos-delay="100">
        <?php foreach(['all'=>'TOUS','web'=>'WEB','design'=>'DESIGN','software'=>'LOGICIEL'] as $v=>$l): ?>
        <button class="tag filter-btn <?= $v==='all'?'accent':'' ?>" data-filter="<?= $v ?>"><?= $l ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-px projects-grid" style="background:var(--border)">
      <?php $catIcons=['web'=>'🌐','design'=>'🎨','software'=>'⚙️','other'=>'📦'];
      foreach($projects as $i=>$p): ?>
      <article class="proj-item card" style="border-radius:0;border:none;padding:0" data-category="<?= $p['category'] ?>" data-aos="fade-up" data-aos-delay="<?= ($i%3)*100 ?>">
        <!-- Image -->
        <div class="proj-img">
          <?php if($p['image'] && file_exists('uploads/projects/'.$p['image'])): ?>
            <img src="uploads/projects/<?= h($p['image']) ?>" alt="<?= h($p['title']) ?>" class="w-full h-full object-cover">
          <?php else: ?>
            <div class="proj-img-placeholder"><?= $catIcons[$p['category']] ?? '📦' ?></div>
          <?php endif; ?>
          <?php if($p['is_featured']): ?>
          <div class="absolute top-3 left-3 px-2 py-1" style="background:var(--accent);color:#0a0a0a;font-size:0.55rem;font-family:'Syne',sans-serif;font-weight:800;letter-spacing:0.15em">★ FEATURED</div>
          <?php endif; ?>
        </div>
        <!-- Content -->
        <div style="padding:1.5rem">
          <div class="flex items-center gap-2 mb-3">
            <span class="proj-cat" style="color:var(--accent);font-family:'DM Mono',monospace"><?= strtoupper($p['category']) ?></span>
          </div>
          <h3 class="font-display font-bold mb-2" style="font-size:1rem"><?= h($p['title']) ?></h3>
          <p style="font-size:0.78rem;color:var(--text2);line-height:1.7;margin-bottom:1rem"><?= h(mb_strimwidth($p['description'],0,100,'…')) ?></p>
          <!-- Tags -->
          <div class="flex flex-wrap gap-1 mb-4">
            <?php foreach(array_slice(tagsArray($p['tech_stack']),0,4) as $tag): ?>
            <span class="tag" style="font-size:0.58rem"><?= h($tag) ?></span>
            <?php endforeach; ?>
          </div>
          <!-- Links -->
          <div class="flex gap-3">
            <?php if($p['link_live']): ?>
            <a href="<?= h($p['link_live']) ?>" target="_blank" class="btn-acid" style="padding:8px 16px;font-size:0.65rem">VOIR →</a>
            <?php endif; ?>
            <?php if($p['link_code']): ?>
            <a href="<?= h($p['link_code']) ?>" target="_blank" class="btn-outline" style="padding:7px 16px;font-size:0.65rem">CODE</a>
            <?php endif; ?>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ================================================================
     PARCOURS — DIPLÔMES & EXPÉRIENCES
     ================================================================ -->
<section id="resume" class="py-32" style="border-top:1px solid var(--border)">
  <div class="max-w-7xl mx-auto px-6">
    <div class="mb-16" data-aos="fade-up">
      <span class="section-num">// 04</span>
      <h2 class="section-title mt-2">Parcours<br><span class="font-serif italic" style="color:var(--text2)">& Formation.</span></h2>
    </div>

    <div class="grid md:grid-cols-2 gap-16">
      <!-- DIPLÔMES -->
      <div>
        <div class="flex items-center gap-3 mb-8" data-aos="fade-up">
          <span style="font-size:0.7rem;letter-spacing:0.2em;color:var(--accent);font-family:'DM Mono',monospace">FORMATIONS</span>
          <div class="flex-1 h-px" style="background:var(--border)"></div>
        </div>
        <div class="relative pl-8" style="border-left:1px solid var(--border)">
          <?php foreach($diplomas as $i=>$d): ?>
          <div class="relative mb-10 last:mb-0" data-aos="fade-up" data-aos-delay="<?= $i*100 ?>">
            <div class="tl-dot"></div>
            <div style="font-size:0.65rem;letter-spacing:0.15em;color:var(--accent);font-family:'DM Mono',monospace;margin-bottom:6px">
              <?= $d['year_start'] ?> — <?= $d['year_end'] ?? 'EN COURS' ?>
            </div>
            <h3 class="font-display font-bold mb-1" style="font-size:1rem"><?= $d['badge_icon'] ?> <?= h($d['title']) ?></h3>
            <div style="font-size:0.78rem;color:var(--text2);margin-bottom:6px"><?= h($d['institution']) ?></div>
            <?php if($d['description']): ?>
            <p style="font-size:0.75rem;color:var(--text2);line-height:1.7"><?= h($d['description']) ?></p>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- EXPÉRIENCES -->
      <div>
        <div class="flex items-center gap-3 mb-8" data-aos="fade-up">
          <span style="font-size:0.7rem;letter-spacing:0.2em;color:var(--accent);font-family:'DM Mono',monospace">EXPÉRIENCES</span>
          <div class="flex-1 h-px" style="background:var(--border)"></div>
        </div>
        <div class="relative pl-8" style="border-left:1px solid var(--border)">
          <?php foreach($experiences as $i=>$e): ?>
          <div class="relative mb-10 last:mb-0" data-aos="fade-up" data-aos-delay="<?= $i*100 ?>">
            <div class="tl-dot"></div>
            <div style="font-size:0.65rem;letter-spacing:0.15em;color:var(--accent);font-family:'DM Mono',monospace;margin-bottom:6px">
              <?= date('M Y',strtotime($e['date_start'])) ?> — <?= $e['date_end'] ? date('M Y',strtotime($e['date_end'])) : 'PRÉSENT' ?>
            </div>
            <h3 class="font-display font-bold mb-1" style="font-size:1rem"><?= h($e['job_title']) ?></h3>
            <div style="font-size:0.78rem;color:var(--text2);margin-bottom:6px"><?= h($e['company']) ?> <?= $e['location']?'· '.h($e['location']):'' ?></div>
            <p style="font-size:0.75rem;color:var(--text2);line-height:1.7;margin-bottom:8px"><?= h($e['description']) ?></p>
            <div class="flex flex-wrap gap-1">
              <?php foreach(tagsArray($e['tags']) as $tag): ?>
              <span class="tag" style="font-size:0.58rem"><?= h($tag) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ================================================================
     CONTACT
     ================================================================ -->
<section id="contact" class="py-32" style="background:var(--bg2); border-top:1px solid var(--border)">
  <div class="max-w-7xl mx-auto px-6">
    <div class="mb-16" data-aos="fade-up">
      <span class="section-num">// 05</span>
      <h2 class="section-title mt-2">Travaillons<br><span class="font-serif italic" style="color:var(--accent)">ensemble.</span></h2>
    </div>

    <div class="grid md:grid-cols-5 gap-16">
      <!-- Infos -->
      <div class="md:col-span-2 space-y-8" data-aos="fade-right">
        <p style="color:var(--text2);line-height:1.8;font-size:0.9rem">Vous avez un projet en tête ? Un site à créer, un logiciel à développer, une identité visuelle à concevoir ? Discutons-en.</p>

        <?php foreach([
          ['📍','Localisation',SITE_LOCATION],
          ['📧','Email',SITE_EMAIL],
          ['📱','Téléphone',SITE_PHONE],
        ] as [$icon,$label,$val]): ?>
        <div class="flex gap-4 items-start">
          <span class="text-xl mt-0.5"><?= $icon ?></span>
          <div>
            <div style="font-size:0.65rem;letter-spacing:0.15em;color:var(--text2);font-family:'DM Mono',monospace;margin-bottom:3px"><?= $label ?></div>
            <div style="font-size:0.9rem;color:var(--text)"><?= h($val) ?></div>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- Réseaux -->
        <div class="flex gap-3 pt-4">
          <?php foreach([['LI','#','LinkedIn'],['GH','#','GitHub'],['BE','#','Behance']] as [$abbr,$url,$name]): ?>
          <a href="<?= $url ?>" target="_blank" class="btn-outline" style="padding:8px 14px;font-size:0.65rem;letter-spacing:0.1em"><?= $abbr ?></a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Formulaire -->
      <div class="md:col-span-3" data-aos="fade-left" data-aos-delay="150">
        <?php if($formMsg): ?>
        <div class="mb-6 px-4 py-3 text-sm" style="background:rgba(200,241,53,0.1);border:1px solid var(--accent);color:var(--accent);font-family:'DM Mono',monospace;font-size:0.8rem">
          ✓ <?= h($formMsg) ?>
        </div>
        <?php endif; ?>
        <?php if($formErr): ?>
        <div class="mb-6 px-4 py-3" style="background:rgba(255,100,100,0.1);border:1px solid #ff6464;color:#ff6464;font-family:'DM Mono',monospace;font-size:0.8rem">
          ✕ <?= h($formErr) ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
          <input type="hidden" name="contact_submit" value="1">
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label style="font-size:0.65rem;letter-spacing:0.15em;color:var(--text2);font-family:'DM Mono',monospace;display:block;margin-bottom:8px">NOM *</label>
              <input name="name" type="text" class="field" placeholder="Votre nom" required value="<?= h($_POST['name']??'') ?>">
            </div>
            <div>
              <label style="font-size:0.65rem;letter-spacing:0.15em;color:var(--text2);font-family:'DM Mono',monospace;display:block;margin-bottom:8px">EMAIL *</label>
              <input name="email" type="email" class="field" placeholder="votre@email.com" required value="<?= h($_POST['email']??'') ?>">
            </div>
          </div>
          <div>
            <label style="font-size:0.65rem;letter-spacing:0.15em;color:var(--text2);font-family:'DM Mono',monospace;display:block;margin-bottom:8px">SUJET *</label>
            <input name="subject" type="text" class="field" placeholder="Votre projet en une ligne" required value="<?= h($_POST['subject']??'') ?>">
          </div>
          <div>
            <label style="font-size:0.65rem;letter-spacing:0.15em;color:var(--text2);font-family:'DM Mono',monospace;display:block;margin-bottom:8px">MESSAGE *</label>
            <textarea name="message" class="field" rows="5" placeholder="Décrivez votre projet, vos besoins, votre budget..." required><?= h($_POST['message']??'') ?></textarea>
          </div>
          <button type="submit" class="btn-acid w-full justify-center" style="font-size:0.8rem">
            ENVOYER LE MESSAGE →
          </button>
        </form>
      </div>
    </div>
  </div>
</section>


<!-- ================================================================
     FOOTER
     ================================================================ -->
<footer style="border-top:1px solid var(--border); padding:2rem 0;">
  <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <span style="font-family:'DM Mono',monospace;font-size:0.7rem;color:var(--text2)">© <?= date('Y') ?> Mouhamed Diallo — Tous droits réservés</span>
    <span style="font-family:'DM Mono',monospace;font-size:0.7rem;color:var(--text2)">Fait avec <span style="color:var(--accent)">♥</span> à Dakar</span>
  </div>
</footer>


<!-- ================================================================
     SCRIPTS
     ================================================================ -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init({ duration:800, easing:'cubic-bezier(0.16,1,0.3,1)', once:true, offset:80 });

/* ---- THEME SWITCHER ---- */
const html = document.documentElement;
const toggle = document.getElementById('themeToggle');
const savedTheme = localStorage.getItem('md-theme') || 'dark';
html.dataset.theme = savedTheme;

toggle.addEventListener('click', () => {
  const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
  html.dataset.theme = next;
  localStorage.setItem('md-theme', next);
});

/* ---- SCROLL PROGRESS ---- */
const scrollBar = document.getElementById('scrollBar');
window.addEventListener('scroll', () => {
  const pct = window.scrollY / (document.body.scrollHeight - window.innerHeight) * 100;
  scrollBar.style.width = pct + '%';
}, { passive: true });

/* ---- CUSTOM CURSOR ---- */
if (window.innerWidth > 768) {
  const cursor = document.getElementById('cursor');
  const ring   = document.getElementById('cursorRing');
  let rx = -100, ry = -100;

  document.addEventListener('mousemove', e => {
    cursor.style.left = e.clientX + 'px';
    cursor.style.top  = e.clientY + 'px';
    rx += (e.clientX - rx) * 0.1;
    ry += (e.clientY - ry) * 0.1;
    ring.style.left = rx + 'px';
    ring.style.top  = ry + 'px';
  });

  document.addEventListener('mouseover', e => {
    if (e.target.closest('a,button,.card')) {
      cursor.style.transform = 'translate(-50%,-50%) scale(2)';
      ring.style.width = '60px'; ring.style.height = '60px';
    }
  });
  document.addEventListener('mouseout', e => {
    if (e.target.closest('a,button,.card')) {
      cursor.style.transform = 'translate(-50%,-50%) scale(1)';
      ring.style.width = '36px'; ring.style.height = '36px';
    }
  });
}

/* ---- SKILL BARS ---- */
const skillObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.querySelectorAll('.skill-fill').forEach(bar => {
        bar.style.width = bar.dataset.width + '%';
      });
      skillObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.3 });
document.querySelectorAll('.card').forEach(c => skillObserver.observe(c));

/* ---- PROJECT FILTER ---- */
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const filter = btn.dataset.filter;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('accent'));
    btn.classList.add('accent');

    document.querySelectorAll('.proj-item').forEach(item => {
      const show = filter === 'all' || item.dataset.category === filter;
      item.style.transition = 'opacity 0.3s, transform 0.3s';
      if (show) {
        item.style.opacity = '1'; item.style.transform = 'scale(1)'; item.style.display = '';
      } else {
        item.style.opacity = '0'; item.style.transform = 'scale(0.95)';
        setTimeout(() => { if(item.style.opacity==='0') item.style.display = 'none'; }, 300);
      }
    });
  });
});

/* ---- HAMBURGER ---- */
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
hamburger.addEventListener('click', () => mobileMenu.classList.toggle('open'));

/* ---- SMOOTH SCROLL ---- */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (!target) return;
    e.preventDefault();
    window.scrollTo({ top: target.offsetTop - 64, behavior: 'smooth' });
  });
});

/* ---- RIPPLE EFFECT ---- */
document.querySelectorAll('.btn-acid, .btn-outline').forEach(btn => {
  btn.style.position = 'relative'; btn.style.overflow = 'hidden';
  btn.addEventListener('click', e => {
    const r = document.createElement('span');
    const rect = btn.getBoundingClientRect();
    Object.assign(r.style, {
      position:'absolute', borderRadius:'50%',
      width:'10px', height:'10px',
      left:(e.clientX-rect.left-5)+'px', top:(e.clientY-rect.top-5)+'px',
      background:'rgba(255,255,255,0.25)',
      transform:'scale(0)', animation:'rippleKf 0.6s ease-out forwards',
      pointerEvents:'none'
    });
    btn.appendChild(r);
    setTimeout(()=>r.remove(),700);
  });
});

/* ---- KEYFRAMES DYNAMIC ---- */
const style = document.createElement('style');
style.textContent = `@keyframes rippleKf { to { transform:scale(30); opacity:0; } }`;
document.head.appendChild(style);
</script>
</body>
</html>
