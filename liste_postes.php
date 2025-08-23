<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Supprimer un poste si demandé
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM postes WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: liste_postes.php"); // éviter re-soumission
    exit();
}

// Récupérer les postes avec département
$stmt = $pdo->query("SELECT p.*, d.nom AS departement_nom 
                     FROM postes p 
                     LEFT JOIN departements d ON p.departement_id = d.id 
                     ORDER BY p.created_at DESC");
$postes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Postes</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
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
        <h1>Liste des Postes</h1>

        <div class="actions">
            <a href="add_poste.php" class="btn btn-add">+ Ajouter un poste</a>
            <a href="dashboard.php" class="btn btn-dashboard">⬅ Retour au Dashboard</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Nom du Poste</th>
                <th>Département</th>
                <th>Description</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
            <?php if (count($postes) > 0): ?>
                <?php foreach ($postes as $poste): ?>
                    <tr>
                        <td><?= htmlspecialchars($poste['id']) ?></td>
                        <td><?= htmlspecialchars($poste['nom']) ?></td>
                        <td><?= htmlspecialchars($poste['departement_nom']) ?></td>
                        <td><?= htmlspecialchars($poste['description']) ?></td>
                        <td><?= htmlspecialchars($poste['created_at']) ?></td>
                        <td>
                            <a href="?delete=<?= $poste['id'] ?>" class="btn btn-delete" onclick="return confirm('Supprimer ce poste ?')">🗑 Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Aucun poste trouvé.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
