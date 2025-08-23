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

// Statistiques générales
$totalEmployes = $pdo->query("SELECT COUNT(*) FROM employes")->fetchColumn();
$totalDepartements = $pdo->query("SELECT COUNT(*) FROM departements")->fetchColumn();

// Congés par type
$congesTypes = $pdo->query("SELECT type, COUNT(*) as total FROM conges GROUP BY type")->fetchAll(PDO::FETCH_ASSOC);
$congesLabels = [];
$congesData = [];
foreach($congesTypes as $row){
    $congesLabels[] = $row['type'];
    $congesData[] = $row['total'];
}

// Présences par statut
$presenceData = [];
$presenceLabels = ['Présent','Absent','En retard'];
foreach($presenceLabels as $label){
    $presenceData[] = $pdo->query("SELECT COUNT(*) FROM presences WHERE statut='$label'")->fetchColumn();
}

// Paiements par mois (année en cours)
$paymentsLabels = [];
$paymentsData = [];
for($m=1;$m<=12;$m++){
    $paymentsLabels[] = date('F', mktime(0,0,0,$m,10));
    $paymentsData[] = $pdo->query("SELECT IFNULL(SUM(montant),0) FROM paiements WHERE MONTH(date_paiement)=$m AND YEAR(date_paiement)=YEAR(CURDATE())")->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapports - HRIS</title>
<style>
body { margin:0; font-family: Arial,sans-serif; background:#181824; color:#fff; overflow-x:hidden; }
header { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#10101a; color:#fff; position:fixed; width:100%; top:0; z-index:1001; height:80px; }
header .menu-btn { font-size:28px; cursor:pointer; background:none; border:none; color:#fff; }
header .profile { display:flex; align-items:center; gap:15px; cursor:pointer; position:relative; }
header .profile img { width:70px; height:70px; border-radius:50%; object-fit:cover; border:2px solid #fff; }
.sidebar { position:fixed; top:80px; left:-250px; width:250px; height:100%; background:#202030; overflow-y:auto; transition:0.3s; padding-top:10px; z-index:1000; }
.sidebar.open { left:0; }
.sidebar a { padding:15px 25px; display:block; color:#ecf0f1; text-decoration:none; border-left:3px solid transparent; transition:0.3s; }
.sidebar a:hover { background:#2c2a3c; border-left:3px solid #3498db; }
.sidebar .section { padding:15px 25px; color:#bdc3c7; font-weight:bold; text-transform:uppercase; border-bottom:1px solid #2c2a3c; margin-bottom:10px; }
.content { padding:110px 30px 30px 30px; margin-left:0; transition:0.3s; }
.content.shift { margin-left:250px; }
.card-container { display:flex; flex-wrap:wrap; gap:15px; justify-content:center; margin-top:20px; }
.card { background:#2c2a3c; padding:20px; border-radius:12px; width:300px; text-align:center; box-shadow:0 4px 10px rgba(0,0,0,0.3); }
.card h3 { margin-bottom:10px; }
canvas { background:#2c2a3c; border-radius:12px; }
.profile-menu { position:absolute; top:80px; right:0; background:#fff; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.3); display:none; min-width:180px; z-index:1001; }
.profile-menu a { display:block; padding:10px; color:#2c3e50; text-decoration:none; }
.profile-menu a:hover { background:#f1f1f1; }
@media(max-width:768px){ header .profile img { width:50px; height:50px; } .card { width:100%; max-width:350px; } }
.export-btn {
    display:inline-block;
    margin:5px;
    padding:10px 18px;
    background:#3498db;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    font-weight:bold;
    transition:0.3s;
}
.export-btn:hover {
    background:#2980b9;
}

</style>
</head>
<body>

<header>
    <button class="menu-btn" id="menuBtn">☰</button>
    <div style="flex:1;text-align:center;color:#fff;font-size:18px; font-weight:bold;">
        Rapport - HRIS
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

<a href="export_csv.php?type=presences">Exporter Présences</a>
<a href="export_csv.php?type=conges">Exporter Congés</a>
<a href="export_csv.php?type=paiements">Exporter Paiements</a>
<a href="export_csv.php?type=performances">Exporter Performances</a>

<div class="card-container">
    <div class="card">
        <h3>Congés par type</h3>
        <canvas id="congesChart" width="280" height="200"></canvas>
    </div>
    <div class="card">
        <h3>Présences des employés</h3>
        <canvas id="presenceChart" width="280" height="200"></canvas>
    </div>
    <div class="card">
        <h3>Paiements par mois (€)</h3>
        <canvas id="paymentsChart" width="280" height="200"></canvas>
    </div>
</div>


<div class="card-container">
    <div class="card">
        <h3>Total Employés</h3>
        <p><?= $totalEmployes ?></p>
    </div>
    <div class="card">
        <h3>Total Départements</h3>
        <p><?= $totalDepartements ?></p>
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

// Congés chart
new Chart(document.getElementById('congesChart'), {
    type:'doughnut',
    data:{
        labels: <?= json_encode($congesLabels) ?>,
        datasets:[{
            data: <?= json_encode($congesData) ?>,
            backgroundColor:['#e67e22','#3498db','#e74c3c','#2ecc71','#9b59b6']
        }]
    },
    options:{ responsive:true, plugins:{legend:{position:'bottom'}} }
});

// Présences chart
new Chart(document.getElementById('presenceChart'), {
    type:'bar',
    data:{
        labels: <?= json_encode($presenceLabels) ?>,
        datasets:[{
            label:'Nombre d\'employés',
            data: <?= json_encode($presenceData) ?>,
            backgroundColor:['#2ecc71','#e74c3c','#f1c40f']
        }]
    },
    options:{ responsive:true, plugins:{legend:{display:false}} }
});

// Paiements chart
new Chart(document.getElementById('paymentsChart'), {
    type:'line',
    data:{
        labels: <?= json_encode($paymentsLabels) ?>,
        datasets:[{
            label:'Montant (€)',
            data: <?= json_encode($paymentsData) ?>,
            borderColor:'#3498db',
            backgroundColor:'rgba(52,152,219,0.2)',
            fill:true,
            tension:0.4
        }]
    },
    options:{ responsive:true, plugins:{legend:{display:false}} }
});
</script>

</body>
</html>





