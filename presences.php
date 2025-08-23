<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Supprimer une présence
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM presences WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: presences.php");
    exit();
}

// Récupérer les présences avec jointure employé
$stmt = $pdo->query("
    SELECT p.id, p.date, p.statut, p.created_at, e.nom 
    FROM presences p
    JOIN employes e ON p.employe_id = e.id
    ORDER BY p.date DESC
");
$presences = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Présences</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
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
        <h1>Liste des Présences</h1>

        <div class="actions">
            <a href="add_presence.php" class="btn btn-add">+ Ajouter une présence</a>
            <a href="dashboard.php" class="btn btn-dashboard">⬅ Retour au Dashboard</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Employé</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Date d'enregistrement</th>
                <th>Actions</th>
            </tr>
            <?php if (count($presences) > 0): ?>
                <?php foreach ($presences as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id']) ?></td>
                        <td><?= htmlspecialchars($p['nom']) ?></td>
                        <td><?= htmlspecialchars($p['date']) ?></td>
                        <td><?= htmlspecialchars($p['statut']) ?></td>
                        <td><?= htmlspecialchars($p['created_at']) ?></td>
                        <td>
                            <a href="presences.php?delete=<?= $p['id'] ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Supprimer cette présence ?');">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Aucune présence enregistrée.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
