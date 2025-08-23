<?php
require 'db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les employés pour le formulaire
$stmt = $pdo->query("SELECT id, nom FROM employes ORDER BY nom ASC");
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employe_id = $_POST['employe_id'];
    $montant = $_POST['montant'];
    $date_paiement = $_POST['date_paiement'];

    if (!empty($employe_id) && !empty($montant) && !empty($date_paiement)) {
        $stmt = $pdo->prepare("INSERT INTO paiements (employe_id, montant, date_paiement) VALUES (?, ?, ?)");
        $stmt->execute([$employe_id, $montant, $date_paiement]);

        header("Location: paiements.php");
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
    <title>Ajouter un paiement</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; 
                     box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #007bff; }
        form { display: flex; flex-direction: column; gap: 15px; }
        input, select { padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
        .btn { background: #28a745; color: white; padding: 10px; border: none; border-radius: 6px; cursor: pointer; }
        .btn:hover { background: #218838; }
        .btn-back { background: #6c757d; text-decoration: none; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Ajouter un Paiement</h1>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Employé :</label>
        <select name="employe_id" required>
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($employes as $emp): ?>
                <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Montant :</label>
        <input type="number" step="0.01" name="montant" required>

        <label>Date de paiement :</label>
        <input type="date" name="date_paiement" required>

        <button type="submit" class="btn">Enregistrer</button>
        <a href="paiements.php" class="btn btn-back">⬅ Retour</a>
    </form>
</div>
</body>
</html>
