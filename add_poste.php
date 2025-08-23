<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer la liste des départements pour associer le poste
$stmt = $pdo->query("SELECT * FROM departements ORDER BY nom ASC");
$departements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $departement_id = $_POST['departement_id'];

    if (!empty($nom) && !empty($departement_id)) {
        $stmt = $pdo->prepare("INSERT INTO postes (departement_id, nom, description) VALUES (?, ?, ?)");
        $stmt->execute([$departement_id, $nom, $description]);
        header("Location: liste_postes.php");
        exit();
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un poste</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #007bff; }
        form { display: flex; flex-direction: column; }
        label { margin-top: 10px; font-weight: bold; }
        input, textarea, select { padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; }
        button { margin-top: 15px; padding: 10px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background: #218838; }
        .btn-back { display: inline-block; margin-top: 15px; text-decoration: none; padding: 10px; background: #6c757d; color: white; border-radius: 6px; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Ajouter un Poste</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="nom">Nom du poste *</label>
        <input type="text" id="nom" name="nom" required>

        <label for="departement_id">Département *</label>
        <select name="departement_id" id="departement_id" required>
            <option value="">-- Sélectionner un département --</option>
            <?php foreach ($departements as $dep): ?>
                <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="description">Description</label>
        <textarea id="description" name="description"></textarea>

        <button type="submit">Ajouter</button>
    </form>

    <a href="liste_postes.php" class="btn-back">⬅ Retour à la liste</a>
</div>
</body>
</html>
