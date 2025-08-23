<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connexion PDO
$host = "localhost";
$dbname = "hris_project";
$user = "root";
$pass = "Mysql";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base : ".$e->getMessage());
}

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id'=>$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$nom = $user['nom'];
$photo = $user['photo_profil'];

// Récupérer les statistiques
$totalEmployes = $pdo->query("SELECT COUNT(*) FROM employes")->fetchColumn();
$totalDepartements = $pdo->query("SELECT COUNT(*) FROM departements")->fetchColumn();
$congesAttente = $pdo->query("SELECT COUNT(*) FROM conges WHERE statut='En attente'")->fetchColumn();
$presencesToday = $pdo->query("SELECT COUNT(*) FROM presences WHERE date=CURDATE() AND statut='Présent'")->fetchColumn();
$salaryMonth = $pdo->query("SELECT IFNULL(SUM(montant),0) FROM paiements WHERE MONTH(date_paiement)=MONTH(CURDATE()) AND YEAR(date_paiement)=YEAR(CURDATE())")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard - HRIS</title>
<style>
* { box-sizing: border-box; margin:0; padding:0; }

body {
    font-family: Arial, sans-serif;
    background:#181824;
    color:#fff;
    overflow-x:hidden; /* Pas de scroll horizontal */
}

header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 30px;
    background:#10101a;
    color:#fff;
    position:fixed;
    width:100%;
    top:0;
    z-index:1001;
    height:80px;
}

header .menu-btn {
    font-size:28px;
    cursor:pointer;
    background:none;
    border:none;
    color:#fff;
    transition:0.3s;
}
header .menu-btn:hover { color:#3498db; }

header .profile {
    display:flex;
    align-items:center;
    gap:15px;
    cursor:pointer;
    position:relative;
}

header .profile img {
    width:70px;
    height:70px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #fff;
}

/* Sidebar */
.sidebar {
    position:fixed;
    top:80px; /* sous header */
    left:-250px;
    width:250px;
    height:100%;
    background:#202030;
    overflow-y:auto;
    transition:0.3s;
    padding-top:10px;
    box-shadow:2px 0 10px rgba(0,0,0,0.5);
    z-index:1000;
}
.sidebar.open { left:0; }
.sidebar a {
    padding:15px 25px;
    display:block;
    color:#ecf0f1;
    text-decoration:none;
    border-left:3px solid transparent;
    transition:0.3s;
}
.sidebar a:hover { background:#2c2a3c; border-left:3px solid #3498db; }
.sidebar .section {
    padding:15px 25px;
    color:#bdc3c7;
    font-weight:bold;
    text-transform:uppercase;
    border-bottom:1px solid #2c2a3c;
    margin-bottom:10px;
}

/* Content */
.content {
    padding:110px 30px 30px 30px;
    margin-left:0;
    transition:0.3s;
}
.content.shift { margin-left:250px; }

.card-container {
    display:flex;
    flex-wrap:wrap;
    gap:15px;
    justify-content:center;
    margin-top:20px;
}
.card {
    background:#2c2a3c;
    padding:20px;
    border-radius:12px;
    width:200px;
    text-align:center;
    position:relative;
    overflow:hidden;
}
.card h3 { margin:0 0 10px; font-size:16px; color:#ecf0f1; }
.card p { margin:0; font-size:24px; font-weight:bold; color:#fff; }
.card canvas { margin-top:10px; }
.card .color-bar { position:absolute; bottom:0; left:0; height:5px; width:100%; }
.color-dept { background:#e67e22; }
.color-emp { background:#3498db; }
.color-conge { background:#e74c3c; }
.color-pres { background:#2ecc71; }
.color-salary { background:#9b59b6; }

.profile-menu {
    position:absolute;
    top:80px;
    right:0;
    background:#fff;
    border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
    display:none;
    min-width:180px;
    z-index:1001;
}
.profile-menu a {
    display:block;
    padding:10px;
    color:#2c3e50;
    text-decoration:none;
}
.profile-menu a:hover { background:#f1f1f1; }

.hero {
    background:#2c2a3c;
    padding:25px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
    text-align:center;
}
.hero h2 { margin:0 0 10px; }

.alert {
    background:#ff6b6b;
    color:#fff;
    padding:10px;
    border-radius:6px;
    margin-bottom:20px;
    display:inline-block;
}

::-webkit-scrollbar { width:8px; }
::-webkit-scrollbar-thumb { background:#3498db; border-radius:4px; }

@media(max-width:768px){
    header .profile img { width:50px; height:50px; }
    .card { width:100%; max-width:300px; }
}
</style>
</head>
<body>

<header>
    <button class="menu-btn" id="menuBtn">☰</button>
    <div style="flex:1;text-align:center;color:#fff;font-size:18px; font-weight:bold;">
        Welcome <?= htmlspecialchars($nom) ?> 👋
    </div>
    <div class="profile" id="profileBtn">
        <img src="uploads/<?= htmlspecialchars($photo) ?>" alt="Avatar">
        <div class="profile-menu" id="profileMenu">
            <a href="profile.php">Modifier Profil</a>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>
</header>

<nav class="sidebar" id="sidebar">
    <div class="section">Menu</div>
    <a href="dashboard.php">Dashboard</a>
    <a href="departements.php">Départements</a>
    <a href="liste_postes.php">Postes</a>
    <a href="liste_employes.php">Employés</a>
    <a href="conges.php">Congés</a>
    <a href="presences.php">Présences</a>
    <a href="paiements.php">Paiements</a>
    <a href="rapport.php">Rapports</a>
</nav>

<main class="content" id="content">
    <?php if($congesAttente > 0): ?>
    <div class="alert">⚠ Vous avez <?= $congesAttente ?> congé(s) en attente!</div>
    <?php endif; ?>

    <div class="hero">
        <h2>Bienvenue sur le HRIS</h2>
        <p>Gérez vos départements, postes, employés, congés, présences et paiements depuis un seul tableau de bord.</p>
    </div>

    <div class="card-container">
        <div class="card">
            <h3>Total Départements</h3>
            <p class="counter"><?= $totalDepartements ?></p>
            <canvas id="chartDept" width="180" height="50"></canvas>
            <div class="color-bar color-dept"></div>
        </div>
        <div class="card">
            <h3>Total Employés</h3>
            <p class="counter"><?= $totalEmployes ?></p>
            <canvas id="chartEmp" width="180" height="50"></canvas>
            <div class="color-bar color-emp"></div>
        </div>
        <div class="card">
            <h3>Congés en attente</h3>
            <p class="counter"><?= $congesAttente ?></p>
            <canvas id="chartConge" width="180" height="50"></canvas>
            <div class="color-bar color-conge"></div>
        </div>
        <div class="card">
            <h3>Présences aujourd'hui</h3>
            <p class="counter"><?= $presencesToday ?></p>
            <canvas id="chartPres" width="180" height="50"></canvas>
            <div class="color-bar color-pres"></div>
        </div>
        <div class="card">
            <h3>Salaire ce mois</h3>
            <p class="counter"><?= number_format($salaryMonth,2,',','.') ?> €</p>
            <canvas id="chartSalary" width="180" height="50"></canvas>
            <div class="color-bar color-salary"></div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const menuBtn = document.getElementById('menuBtn');
const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');

menuBtn.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    content.classList.toggle('shift');
});

const profileBtn = document.getElementById('profileBtn');
const profileMenu = document.getElementById('profileMenu');
profileBtn.addEventListener('click', () => {
    profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
});

// Animate counters
document.querySelectorAll('.counter').forEach(el => {
    let count = 0;
    const target = parseFloat(el.textContent.replace(/[^\d\.]/g,''));
    const increment = target / 100;
    const interval = setInterval(() => {
        count += increment;
        if(count >= target){
            el.textContent = el.textContent.includes('€') ? target.toFixed(2)+' €' : target;
            clearInterval(interval);
        } else {
            el.textContent = el.textContent.includes('€') ? count.toFixed(2)+' €' : Math.floor(count);
        }
    }, 15);
});

// Mini charts
function createMiniChart(ctx, data, color){
    new Chart(ctx, {
        type:'line',
        data:{labels:Array(data.length).fill(''), datasets:[{data:data,borderColor:color,borderWidth:2,fill:false,tension:0.4,pointRadius:0}]},
        options:{responsive:false,plugins:{legend:{display:false}},scales:{x:{display:false},y:{display:false}}}
    });
}

createMiniChart(document.getElementById('chartDept'), [<?= $totalDepartements ?>, <?= $totalDepartements-1 ?>, <?= $totalDepartements ?>], '#e67e22');
createMiniChart(document.getElementById('chartEmp'), [<?= $totalEmployes ?>, <?= $totalEmployes-2 ?>, <?= $totalEmployes ?>], '#3498db');
createMiniChart(document.getElementById('chartConge'), [<?= $congesAttente ?>, <?= max(0,$congesAttente-1) ?>, <?= $congesAttente ?>], '#e74c3c');
createMiniChart(document.getElementById('chartPres'), [<?= $presencesToday ?>, <?= max(0,$presencesToday-1) ?>, <?= $presencesToday ?>], '#2ecc71');
createMiniChart(document.getElementById('chartSalary'), [<?= $salaryMonth ?>, <?= $salaryMonth*0.9 ?>, <?= $salaryMonth ?>], '#9b59b6');
</script>

</body>
</html>





