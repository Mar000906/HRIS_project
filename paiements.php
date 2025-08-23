<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Supprimer un paiement
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM paiements WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: paiements.php");
    exit();
}

// Récupérer les paiements avec infos employé
$stmt = $pdo->query("
    SELECT p.id, e.nom AS employe_nom, p.montant, p.date_paiement, p.created_at 
    FROM paiements p
    JOIN employes e ON p.employe_id = e.id
    ORDER BY p.date_paiement DESC
");
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Paiements</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; 
                     box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #007bff; }
        .actions { text-align: center; margin-bottom: 20px; }
        .btn { padding: 10px 18px; border-radius: 6px; text-decoration: none; color: white; margin: 5px; display: inline-block; }
        .btn-add { background: #28a745; }
        .btn-dashboard { background: #6c757d; }
        .btn-delete { background: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
<div class="container">
    <h1>Liste des Paiements</h1>

    <div class="actions">
        <a href="add_paiement.php" class="btn btn-add">+ Ajouter un paiement</a>
        <a href="dashboard.php" class="btn btn-dashboard">⬅ Retour au Dashboard</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Employé</th>
            <th>Montant</th>
            <th>Date de paiement</th>
            <th>Créé le</th>
            <th>Action</th>
        </tr>
        <?php if (count($paiements) > 0): ?>
            <?php foreach ($paiements as $paiement): ?>
                <tr>
                    <td><?= htmlspecialchars($paiement['id']) ?></td>
                    <td><?= htmlspecialchars($paiement['employe_nom']) ?></td>
                    <td><?= htmlspecialchars($paiement['montant']) ?> MAD</td>
                    <td><?= htmlspecialchars($paiement['date_paiement']) ?></td>
                    <td><?= htmlspecialchars($paiement['created_at']) ?></td>
                    <td>
                        <a href="paiements.php?delete=<?= $paiement['id'] ?>" class="btn btn-delete" onclick="return confirm('Supprimer ce paiement ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Aucun paiement trouvé.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
