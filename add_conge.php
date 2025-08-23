<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer la liste des employés
$stmt = $pdo->query("SELECT id, nom FROM employes ORDER BY nom ASC");
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employe_id = $_POST['employe_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin   = $_POST['date_fin'];

    if (!empty($employe_id) && !empty($date_debut) && !empty($date_fin)) {
        // Insertion dans la table conges
        $stmt = $pdo->prepare("INSERT INTO conges (employe_id, date_debut, date_fin, statut, created_at) 
                               VALUES (:emp, :debut, :fin, :statut, NOW())");
        $stmt->execute([
            ':emp'    => $employe_id,
            ':debut'  => $date_debut,
            ':fin'    => $date_fin,
            ':statut' => 'En attente'
        ]);

        header("Location: conges.php");
        exit();
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Congé</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #007bff; }
        form { display: flex; flex-direction: column; gap: 15px; }
        label { font-weight: bold; }
        input, select { padding: 10px; border: 1px solid #ccc; border-radius: 6px; width: 100%; }
        button { padding: 12px; border: none; border-radius: 6px; background: #28a745; color: white; font-size: 16px; cursor: pointer; }
        button:hover { background: #218838; }
        .btn-back { display: inline-block; margin-top: 10px; padding: 10px 18px; background: #6c757d; color: white; border-radius: 6px; text-decoration: none; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Ajouter un Congé</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="employe_id">Employé :</label>
        <select name="employe_id" id="employe_id" required>
            <option value="">-- Sélectionner un employé --</option>
            <?php foreach ($employes as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="date_debut">Date début :</label>
        <input type="date" name="date_debut" id="date_debut" required>

        <label for="date_fin">Date fin :</label>
        <input type="date" name="date_fin" id="date_fin" required>

        <button type="submit">Enregistrer</button>
    </form>

    <a href="conges.php" class="btn-back">⬅ Retour à la liste</a>
</div>
</body>
</html>
