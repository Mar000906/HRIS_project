<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Supprimer un employé si demandé
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM employes WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: liste_employes.php");
    exit();
}

// Récupérer les employés avec poste
$stmt = $pdo->query("SELECT e.*, p.nom AS poste_nom 
                     FROM employes e 
                     LEFT JOIN postes p ON e.poste_id = p.id
                     ORDER BY e.created_at DESC");
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Employés</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
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
        img { width: 50px; height: 50px; border-radius: 50%; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Liste des Employés</h1>

        <div class="actions">
            <a href="add_employe.php" class="btn btn-add">+ Ajouter un employé</a>
            <a href="dashboard.php" class="btn btn-dashboard">⬅ Retour au Dashboard</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Poste</th>
                <th>Date d'embauche</th>
                <th>Salaire</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
            <?php if (count($employes) > 0): ?>
                <?php foreach ($employes as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['id']) ?></td>
                        <td><?= htmlspecialchars($emp['nom']) ?></td>
                        <td><?= htmlspecialchars($emp['email']) ?></td>
                        <td><?= htmlspecialchars($emp['telephone']) ?></td>
                        <td><?= htmlspecialchars($emp['adresse']) ?></td>
                        <td><?= htmlspecialchars($emp['poste_nom']) ?></td>
                        <td><?= htmlspecialchars($emp['date_embauche']) ?></td>
                        <td><?= htmlspecialchars($emp['salaire']) ?> €</td>
                        <td><img src="uploads/<?= htmlspecialchars($emp['photo']) ?>" alt="Photo"></td>
                        <td>
                            <a href="?delete=<?= $emp['id'] ?>" class="btn btn-delete" onclick="return confirm('Supprimer cet employé ?')">🗑 Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10">Aucun employé trouvé.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
