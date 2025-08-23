<?php
session_start();
require_once "db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $description = trim($_POST["description"]);

    if (!empty($nom)) {
        try {
            $sql = "INSERT INTO departements (nom, description) VALUES (:nom, :description)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":nom" => $nom,
                ":description" => $description
            ]);
            $message = "<div class='success'>✅ Département ajouté avec succès !</div>";
        } catch (PDOException $e) {
            $message = "<div class='error'>❌ Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='error'>⚠️ Le nom du département est obligatoire.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Département</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 500px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background: #219150;
        }
        .btn-secondary {
            background: #3498db;
        }
        .btn-secondary:hover {
            background: #2c80b4;
        }
        .message {
            margin-bottom: 20px;
            text-align: center;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Ajouter un Département</h2>

    <div class="message">
        <?php if ($message) echo $message; ?>
    </div>

    <form method="post">
        <div class="form-group">
            <label for="nom">Nom du département :</label>
            <input type="text" id="nom" name="nom" required>
        </div>

        <div class="form-group">
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <button type="submit">➕ Ajouter</button>
        <a href="departements.php"><button type="button" class="btn-secondary">↩️ Retour Liste</button></a>
        <a href="dashboard.php"><button type="button" class="btn-secondary">🏠 Dashboard</button></a>
    </form>
</div>
</body>
</html>
