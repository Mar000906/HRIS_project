<?php
require 'db.php';
session_start();

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ Suppression d'un département (si ?delete=ID est passé dans l'URL)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM departements WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $message = "<div class='success'>✅ Département supprimé avec succès !</div>";
    } catch (PDOException $e) {
        $message = "<div class='error'>❌ Erreur : " . $e->getMessage() . "</div>";
    }
}

// Récupérer les départements
$stmt = $pdo->query("SELECT * FROM departements ORDER BY created_at DESC");
$departements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des départements</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f4f6f9; 
            margin: 0; 
            padding: 20px; 
        }
        .container { 
            max-width: 1000px; 
            margin: auto; 
            background: #fff; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
        }
        h1 { 
            text-align: center; 
            color: #007bff; 
        }
        .actions {
            text-align: center; 
            margin-bottom: 20px;
        }
        .btn { 
            padding: 10px 18px; 
            border-radius: 6px; 
            text-decoration: none; 
            color: white; 
            margin: 5px; 
            display: inline-block; 
        }
        .btn-add { background: #28a745; }
        .btn-dashboard { background: #6c757d; }
        .btn-delete { background: #dc3545; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            padding: 12px; 
            border: 1px solid #ddd; 
            text-align: center; 
        }
        th { 
            background: #007bff; 
            color: white; 
        }
        tr:nth-child(even) { background: #f9f9f9; }
        .success { 
            background: #d4edda; 
            color: #155724; 
            padding: 10px; 
            border-radius: 6px; 
            margin-bottom: 15px;
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 10px; 
            border-radius: 6px; 
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Liste des Départements</h1>

        <div class="actions">
            <a href="add_departement.php" class="btn btn-add">+ Ajouter un département</a>
            <a href="dashboard.php" class="btn btn-dashboard">⬅ Retour au Dashboard</a>
        </div>

        <!-- ✅ Message succès / erreur -->
        <?php if (!empty($message)) echo $message; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
            <?php if (count($departements) > 0): ?>
                <?php foreach ($departements as $dep): ?>
                    <tr>
                        <td><?= htmlspecialchars($dep['id']) ?></td>
                        <td><?= htmlspecialchars($dep['nom']) ?></td>
                        <td><?= htmlspecialchars($dep['description']) ?></td>
                        <td><?= htmlspecialchars($dep['created_at']) ?></td>
                        <td>
                            <!-- Bouton Supprimer -->
                            <a href="departements.php?delete=<?= $dep['id'] ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Voulez-vous vraiment supprimer ce département ?');">
                                🗑 Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Aucun département trouvé.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>



