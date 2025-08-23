<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les congés avec les infos de l'employé
$stmt = $pdo->query("
    SELECT c.*, e.nom 
    FROM conges c
    JOIN employes e ON c.employe_id = e.id
    ORDER BY c.created_at DESC
");
$conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Congés</title>
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
    <h1>Liste des Congés</h1>

    <div class="actions">
        <a href="add_conge.php" class="btn btn-add">+ Ajouter un congé</a>
        <a href="dashboard.php" class="btn btn-dashboard">⬅ Retour au Dashboard</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Employé</th>
            <th>Date Début</th>
            <th>Date Fin</th>
           
            <th>Date de création</th>
            <th>Action</th>
        </tr>
        <?php if (count($conges) > 0): ?>
            <?php foreach ($conges as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['id']) ?></td>
                    <td><?= htmlspecialchars($c['nom']) ?></td>
                    <td><?= htmlspecialchars($c['date_debut']) ?></td>
                    <td><?= htmlspecialchars($c['date_fin']) ?></td>
                
                    <td><?= htmlspecialchars($c['created_at']) ?></td>
                    <td>
                        <a href="conges.php?delete=<?= $c['id'] ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('Voulez-vous vraiment supprimer ce congé ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Aucun congé trouvé.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>

<?php
// Suppression
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM conges WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: conges.php");
    exit();
}
?>
