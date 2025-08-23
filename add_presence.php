<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ajouter une présence
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employe_id = $_POST['employe_id'];
    $date = $_POST['date'];
    $statut = $_POST['statut'];

    $stmt = $pdo->prepare("INSERT INTO presences (employe_id, date, statut) VALUES (?, ?, ?)");
    $stmt->execute([$employe_id, $date, $statut]);

    header("Location: presences.php");
    exit();
}

// Récupérer les employés pour la liste déroulante
$employes = $pdo->query("SELECT id, nom FROM employes ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Présence</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #007bff; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { margin-top: 15px; background: #28a745; color: #fff; border: none; padding: 10px; border-radius: 5px; cursor: pointer; width: 100%; }
        .btn:hover { background: #218838; }
        .back { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter une Présence</h1>
        <form method="post">
            <label>Employé :</label>
            <select name="employe_id" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($employes as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Date :</label>
            <input type="date" name="date" required>

            <label>Statut :</label>
            <select name="statut" required>
                <option value="present">Présent</option>
                <option value="absent">Absent</option>
                <option value="retard">Retard</option>
            </select>

            <button type="submit" class="btn">Ajouter</button>
        </form>
        <a href="presences.php" class="back">⬅ Retour à la liste</a>
    </div>
</body>
</html>
