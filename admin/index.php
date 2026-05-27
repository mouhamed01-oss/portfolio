<?php
require_once '../config.php';
requireAdmin();
$pdo = db();

$tab = $_GET['tab'] ?? 'diplomas';
$msg = $_GET['msg'] ?? '';

// ---- ACTIONS DELETE ----
if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $tables = ['diplomas'=>'diplomas','experiences'=>'experiences','projects'=>'projects','messages'=>'contact_messages'];
    if (isset($tables[$tab])) {
        $pdo->prepare("DELETE FROM {$tables[$tab]} WHERE id=?")->execute([$id]);
    }
    header("Location: index.php?tab=$tab&msg=deleted"); exit;
}

// ---- ACTIONS MARK READ ----
if (isset($_GET['read']) && $tab === 'messages') {
    $pdo->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([(int)$_GET['read']]);
    header("Location: index.php?tab=messages"); exit;
}

// ---- DATA ----
$diplomas    = $pdo->query("SELECT * FROM diplomas    ORDER BY sort_order,created_at DESC")->fetchAll();
$experiences = $pdo->query("SELECT * FROM experiences ORDER BY sort_order,created_at DESC")->fetchAll();
$projects    = $pdo->query("SELECT * FROM projects    ORDER BY sort_order,created_at DESC")->fetchAll();
$messages    = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$unread      = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();

// Stats
$stats = [
    ['Diplômes',   count($diplomas),    '🎓'],
    ['Expériences',count($experiences), '💼'],
    ['Projets',    count($projects),    '🚀'],
    ['Messages',   count($messages),    '✉️'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — MD Portfolio</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Mono:wght@300;400&display=swap" rel="stylesheet">
<style>
:root { --bg:#0a0a0a; --bg2:#111; --bg3:#181818; --border:rgba(255,255,255,0.07); --text:#f0ede6; --text2:#888; --accent:#c8f135; }
* { box-sizing:border-box; }
body { background:var(--bg); color:var(--text); font-family:'DM Mono',monospace; font-weight:300; min-height:100vh; }
.sidebar { width:240px; background:var(--bg2); border-right:1px solid var(--border); min-height:100vh; position:fixed; top:0; left:0; }
.main { margin-left:240px; padding:2rem; }
.nav-link { display:flex; align-items:center; gap:10px; padding:10px 20px; font-size:0.78rem; letter-spacing:0.08em; color:var(--text2); transition:all 0.2s; border-left:2px solid transparent; }
.nav-link:hover { color:var(--text); background:rgba(255,255,255,0.03); }
.nav-link.active { color:var(--accent); border-left-color:var(--accent); background:rgba(200,241,53,0.05); }
.field { background:var(--bg3); border:1px solid var(--border); padding:10px 14px; width:100%; font-family:'DM Mono',monospace; font-size:0.82rem; color:var(--text); outline:none; transition:border-color 0.2s; border-radius:2px; }
.field:focus { border-color:var(--accent); }
.field::placeholder { color:var(--text2); }
.btn-a { background:var(--accent); color:#0a0a0a; font-family:'Syne',sans-serif; font-weight:700; font-size:0.72rem; letter-spacing:0.1em; text-transform:uppercase; padding:9px 20px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:filter 0.2s; }
.btn-a:hover { filter:brightness(1.1); }
.btn-del { background:rgba(255,80,80,0.1); color:#ff6464; border:1px solid rgba(255,80,80,0.3); font-size:0.7rem; padding:5px 12px; cursor:pointer; font-family:'DM Mono',monospace; transition:background 0.2s; }
.btn-del:hover { background:rgba(255,80,80,0.2); }
.btn-edit { background:rgba(200,241,53,0.08); color:var(--accent); border:1px solid rgba(200,241,53,0.25); font-size:0.7rem; padding:5px 12px; cursor:pointer; font-family:'DM Mono',monospace; transition:background 0.2s; text-decoration:none; }
.btn-edit:hover { background:rgba(200,241,53,0.15); }
.card { background:var(--bg2); border:1px solid var(--border); padding:1.5rem; border-radius:2px; }
.data-table { width:100%; border-collapse:collapse; font-size:0.78rem; }
.data-table th { text-align:left; padding:10px 12px; font-size:0.65rem; letter-spacing:0.15em; color:var(--text2); border-bottom:1px solid var(--border); font-weight:400; }
.data-table td { padding:12px; border-bottom:1px solid rgba(255,255,255,0.03); color:var(--text2); vertical-align:middle; }
.data-table tr:hover td { background:rgba(255,255,255,0.02); }
.data-table .td-main { color:var(--text); font-weight:400; }
.modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:1000; align-items:center; justify-content:center; padding:2rem; }
.modal.open { display:flex; }
.modal-box { background:var(--bg2); border:1px solid var(--border); padding:2rem; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; }
label { font-size:0.65rem; letter-spacing:0.15em; color:var(--text2); display:block; margin-bottom:6px; }
.form-group { margin-bottom:16px; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div style="padding:20px; border-bottom:1px solid var(--border)">
    <div style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem"><span style="color:var(--accent)">MD</span> Admin</div>
    <div style="font-size:0.6rem;letter-spacing:0.15em;color:var(--text2);margin-top:2px">TABLEAU DE BORD</div>
  </div>

  <nav style="padding:16px 0">
    <?php foreach(['diplomas'=>['🎓','DIPLÔMES'],'experiences'=>['💼','EXPÉRIENCES'],'projects'=>['🚀','PROJETS'],'messages'=>['✉️','MESSAGES']] as $key=>[$icon,$label]): ?>
    <a href="index.php?tab=<?= $key ?>" class="nav-link <?= $tab===$key?'active':'' ?>">
      <span><?= $icon ?></span> <?= $label ?>
      <?php if($key==='messages' && $unread>0): ?>
      <span style="margin-left:auto;background:var(--accent);color:#0a0a0a;font-size:0.6rem;padding:2px 6px;font-weight:700;font-family:'Syne',sans-serif"><?= $unread ?></span>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>
  </nav>

  <div style="padding:16px 20px;border-top:1px solid var(--border);margin-top:auto;position:absolute;bottom:0;left:0;right:0">
    <a href="../index.php" style="font-size:0.65rem;letter-spacing:0.1em;color:var(--text2);display:block;margin-bottom:8px">← Voir le portfolio</a>
    <a href="logout.php" style="font-size:0.65rem;letter-spacing:0.1em;color:#ff6464">Déconnexion</a>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <!-- Stats -->
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:32px">
    <?php foreach($stats as [$label,$count,$icon]): ?>
    <div class="card" style="text-align:center">
      <div style="font-size:1.5rem;margin-bottom:4px"><?= $icon ?></div>
      <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--accent)"><?= $count ?></div>
      <div style="font-size:0.65rem;letter-spacing:0.12em;color:var(--text2)"><?= $label ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if($msg): ?>
  <div style="background:rgba(200,241,53,0.1);border:1px solid var(--accent);color:var(--accent);padding:10px 14px;margin-bottom:20px;font-size:0.78rem">
    ✓ <?= $msg==='deleted'?'Élément supprimé.':($msg==='saved'?'Enregistré avec succès.':'Action effectuée.') ?>
  </div>
  <?php endif; ?>

  <!-- ============================================================
       DIPLÔMES
       ============================================================ -->
  <?php if($tab === 'diplomas'): ?>
  <div class="flex items-center justify-between mb-6">
    <h1 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800">🎓 Diplômes & Formations</h1>
    <button class="btn-a" onclick="openModal('modalDiploma')">+ AJOUTER</button>
  </div>

  <div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
      <thead><tr><th>TITRE</th><th>ÉTABLISSEMENT</th><th>PÉRIODE</th><th>ACTIONS</th></tr></thead>
      <tbody>
        <?php foreach($diplomas as $d): ?>
        <tr>
          <td class="td-main"><?= $d['badge_icon'] ?> <?= h($d['title']) ?></td>
          <td><?= h($d['institution']) ?></td>
          <td><?= $d['year_start'] ?> – <?= $d['year_end']??'…' ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="edit_diploma.php?id=<?= $d['id'] ?>" class="btn-edit">ÉDITER</a>
              <a href="index.php?tab=diplomas&del=<?= $d['id'] ?>" class="btn-del" onclick="return confirm('Supprimer ce diplôme ?')">✕</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal ajout diplôme -->
  <div class="modal" id="modalDiploma">
    <div class="modal-box">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800">Ajouter un diplôme</h2>
        <button onclick="closeModal('modalDiploma')" style="color:var(--text2);background:none;border:none;font-size:1.2rem;cursor:pointer">✕</button>
      </div>
      <form method="POST" action="save_diploma.php">
        <input type="hidden" name="action" value="add">
        <div class="form-group"><label>TITRE *</label><input name="title" class="field" required placeholder="Ex : Licence Génie Informatique"></div>
        <div class="form-group"><label>ÉTABLISSEMENT *</label><input name="institution" class="field" required placeholder="Université de Thiès"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group"><label>ANNÉE DÉBUT</label><input name="year_start" type="number" class="field" min="2000" max="2030" placeholder="2021"></div>
          <div class="form-group"><label>ANNÉE FIN</label><input name="year_end" type="number" class="field" min="2000" max="2030" placeholder="2024 (vide=en cours)"></div>
        </div>
        <div class="form-group"><label>DESCRIPTION</label><textarea name="description" class="field" rows="3" placeholder="Contenu de la formation..."></textarea></div>
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px">
          <div class="form-group"><label>ICÔNE</label><input name="badge_icon" class="field" value="🎓" maxlength="4"></div>
          <div class="form-group"><label>ORDRE D'AFFICHAGE</label><input name="sort_order" type="number" class="field" value="0"></div>
        </div>
        <button type="submit" class="btn-a" style="width:100%;justify-content:center">ENREGISTRER</button>
      </form>
    </div>
  </div>

  <!-- ============================================================
       EXPÉRIENCES
       ============================================================ -->
  <?php elseif($tab === 'experiences'): ?>
  <div class="flex items-center justify-between mb-6">
    <h1 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800">💼 Expériences Professionnelles</h1>
    <button class="btn-a" onclick="openModal('modalExp')">+ AJOUTER</button>
  </div>

  <div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
      <thead><tr><th>POSTE</th><th>ENTREPRISE</th><th>PÉRIODE</th><th>ACTIONS</th></tr></thead>
      <tbody>
        <?php foreach($experiences as $e): ?>
        <tr>
          <td class="td-main"><?= h($e['job_title']) ?></td>
          <td><?= h($e['company']) ?></td>
          <td style="font-size:0.7rem;color:var(--accent)"><?= date('M Y',strtotime($e['date_start'])) ?> – <?= $e['date_end']?date('M Y',strtotime($e['date_end'])):'PRÉSENT' ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="edit_experience.php?id=<?= $e['id'] ?>" class="btn-edit">ÉDITER</a>
              <a href="index.php?tab=experiences&del=<?= $e['id'] ?>" class="btn-del" onclick="return confirm('Supprimer cette expérience ?')">✕</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal exp -->
  <div class="modal" id="modalExp">
    <div class="modal-box">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800">Ajouter une expérience</h2>
        <button onclick="closeModal('modalExp')" style="color:var(--text2);background:none;border:none;font-size:1.2rem;cursor:pointer">✕</button>
      </div>
      <form method="POST" action="save_experience.php">
        <input type="hidden" name="action" value="add">
        <div class="form-group"><label>TITRE DU POSTE *</label><input name="job_title" class="field" required placeholder="Développeur Full-Stack"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group"><label>ENTREPRISE *</label><input name="company" class="field" required></div>
          <div class="form-group"><label>LIEU</label><input name="location" class="field" placeholder="Dakar, Sénégal"></div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group"><label>DATE DÉBUT *</label><input name="date_start" type="date" class="field" required></div>
          <div class="form-group"><label>DATE FIN (vide=actuel)</label><input name="date_end" type="date" class="field"></div>
        </div>
        <div class="form-group"><label>DESCRIPTION *</label><textarea name="description" class="field" rows="4" required></textarea></div>
        <div class="form-group"><label>TAGS (séparés par virgule)</label><input name="tags_raw" class="field" placeholder="PHP, MySQL, JavaScript"></div>
        <button type="submit" class="btn-a" style="width:100%;justify-content:center">ENREGISTRER</button>
      </form>
    </div>
  </div>

  <!-- ============================================================
       PROJETS
       ============================================================ -->
  <?php elseif($tab === 'projects'): ?>
  <div class="flex items-center justify-between mb-6">
    <h1 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800">🚀 Projets</h1>
    <button class="btn-a" onclick="openModal('modalProj')">+ AJOUTER</button>
  </div>

  <div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
      <thead><tr><th>TITRE</th><th>CATÉGORIE</th><th>FEATURED</th><th>ACTIONS</th></tr></thead>
      <tbody>
        <?php foreach($projects as $p): ?>
        <tr>
          <td class="td-main"><?= h($p['title']) ?></td>
          <td><span style="color:var(--accent);font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em"><?= $p['category'] ?></span></td>
          <td><?= $p['is_featured']?'<span style="color:var(--accent)">★ OUI</span>':'—' ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="edit_project.php?id=<?= $p['id'] ?>" class="btn-edit">ÉDITER</a>
              <a href="index.php?tab=projects&del=<?= $p['id'] ?>" class="btn-del" onclick="return confirm('Supprimer ce projet ?')">✕</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal projet -->
  <div class="modal" id="modalProj">
    <div class="modal-box">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800">Ajouter un projet</h2>
        <button onclick="closeModal('modalProj')" style="color:var(--text2);background:none;border:none;font-size:1.2rem;cursor:pointer">✕</button>
      </div>
      <form method="POST" action="save_project.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="form-group"><label>TITRE *</label><input name="title" class="field" required></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group">
            <label>CATÉGORIE</label>
            <select name="category" class="field">
              <option value="web">Web</option>
              <option value="design">Design</option>
              <option value="software">Logiciel</option>
              <option value="other">Autre</option>
            </select>
          </div>
          <div class="form-group"><label>ORDRE</label><input name="sort_order" type="number" class="field" value="0"></div>
        </div>
        <div class="form-group"><label>DESCRIPTION *</label><textarea name="description" class="field" rows="4" required></textarea></div>
        <div class="form-group"><label>TECHNOLOGIES (séparées par virgule)</label><input name="tech_raw" class="field" placeholder="PHP, MySQL, Tailwind"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group"><label>LIEN LIVE</label><input name="link_live" class="field" placeholder="https://..."></div>
          <div class="form-group"><label>LIEN CODE</label><input name="link_code" class="field" placeholder="https://github.com/..."></div>
        </div>
        <div class="form-group"><label>IMAGE DU PROJET</label><input name="image" type="file" class="field" accept="image/*" style="padding:8px"></div>
        <div class="form-group" style="display:flex;align-items:center;gap:10px">
          <input type="checkbox" name="is_featured" id="feat" value="1" style="accent-color:var(--accent);width:16px;height:16px">
          <label for="feat" style="margin-bottom:0;cursor:pointer">Mettre en avant (Featured)</label>
        </div>
        <button type="submit" class="btn-a" style="width:100%;justify-content:center">ENREGISTRER</button>
      </form>
    </div>
  </div>

  <!-- ============================================================
       MESSAGES
       ============================================================ -->
  <?php elseif($tab === 'messages'): ?>
  <div class="mb-6">
    <h1 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800">✉️ Messages de Contact
      <?php if($unread>0): ?><span style="background:var(--accent);color:#0a0a0a;font-size:0.7rem;padding:3px 8px;margin-left:8px;font-family:'Syne',sans-serif;font-weight:800"><?= $unread ?> non lu<?= $unread>1?'s':'' ?></span><?php endif; ?>
    </h1>
  </div>

  <div class="card" style="padding:0;overflow:hidden">
    <table class="data-table">
      <thead><tr><th>NOM</th><th>EMAIL</th><th>SUJET</th><th>DATE</th><th>ÉTAT</th><th>ACTION</th></tr></thead>
      <tbody>
        <?php foreach($messages as $m): ?>
        <tr style="<?= !$m['is_read']?'background:rgba(200,241,53,0.03)':'' ?>">
          <td class="td-main" style="<?= !$m['is_read']?'font-weight:600':'' ?>"><?= h($m['name']) ?></td>
          <td style="font-size:0.72rem"><a href="mailto:<?= h($m['email']) ?>" style="color:var(--accent)"><?= h($m['email']) ?></a></td>
          <td><?= h(mb_strimwidth($m['subject'],0,40,'…')) ?></td>
          <td style="font-size:0.7rem"><?= date('d/m/Y H:i',strtotime($m['created_at'])) ?></td>
          <td><?= $m['is_read']?'<span style="color:#888;font-size:0.7rem">LU</span>':'<span style="color:var(--accent);font-size:0.7rem">NOUVEAU</span>' ?></td>
          <td>
            <div style="display:flex;gap:6px">
              <?php if(!$m['is_read']): ?><a href="index.php?tab=messages&read=<?= $m['id'] ?>" class="btn-edit" style="font-size:0.65rem">✓ LU</a><?php endif; ?>
              <button onclick="showMsg(<?= htmlspecialchars(json_encode($m['message'])) ?>, '<?= h(addslashes($m['name'])) ?>')" class="btn-edit" style="font-size:0.65rem">VOIR</button>
              <a href="index.php?tab=messages&del=<?= $m['id'] ?>" class="btn-del" onclick="return confirm('Supprimer ?')">✕</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal message -->
  <div class="modal" id="modalMsg">
    <div class="modal-box">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h2 style="font-family:'Syne',sans-serif;font-weight:800" id="msgTitle">Message de</h2>
        <button onclick="closeModal('modalMsg')" style="color:var(--text2);background:none;border:none;font-size:1.2rem;cursor:pointer">✕</button>
      </div>
      <div id="msgBody" style="font-size:0.85rem;color:var(--text2);line-height:1.8;white-space:pre-wrap;background:var(--bg3);padding:1rem;border:1px solid var(--border)"></div>
    </div>
  </div>
  <?php endif; ?>
</main>

<script>
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

// Fermer modal en cliquant dehors
document.querySelectorAll('.modal').forEach(m => {
  m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); });
});

function showMsg(text, name) {
  document.getElementById('msgTitle').textContent = 'Message de ' + name;
  document.getElementById('msgBody').textContent = text;
  openModal('modalMsg');
}
</script>
</body>
</html>
