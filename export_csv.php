<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    exit("Accès refusé");
}

if(!isset($_GET['type'])) {
    exit("Type de rapport manquant !");
}

$type = $_GET['type'];

$pdo = new PDO("mysql:host=localhost;dbname=hris_project;charset=utf8", "root", "Mysql");

// Définir les colonnes et la requête selon le type
switch($type){
    case 'presences':
        $filename = "presences.csv";
        $columns = ['Nom', 'Date', 'Statut'];
        $query = "SELECT e.nom, p.date, p.statut 
                  FROM presences p 
                  JOIN employes e ON p.employe_id=e.id";
        break;

    case 'conges':
        $filename = "conges.csv";
        $columns = ['Nom', 'Type de congé', 'Date début', 'Date fin', 'Statut'];
        $query = "SELECT e.nom, c.type, c.date_debut, c.date_fin, c.statut
                  FROM conges c
                  JOIN employes e ON c.employe_id=e.id";
        break;

    case 'paiements':
        $filename = "paiements.csv";
        $columns = ['Nom', 'Montant', 'Date paiement', 'Type paiement'];
        $query = "SELECT e.nom, p.montant, p.date_paiement, p.type
                  FROM paiements p
                  JOIN employes e ON p.employe_id=e.id";
        break;

    case 'performances':
        $filename = "performances.csv";
        $columns = ['Nom', 'Score', 'Évaluation', 'Date'];
        $query = "SELECT e.nom, pf.score, pf.commentaire, pf.date_eval
                  FROM performances pf
                  JOIN employes e ON pf.employe_id=e.id";
        break;

    default:
        exit("Type de rapport invalide !");
}

// Définir les headers CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$filename);

$output = fopen('php://output', 'w');
fputcsv($output, $columns);

// Exécuter la requête
$stmt = $pdo->query($query);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    fputcsv($output, $row);
}

fclose($output);
exit;
