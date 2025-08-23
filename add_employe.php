<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les postes
$posts = $pdo->query("SELECT * FROM postes ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Ajouter un employé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $date_embauche = $_POST['date_embauche'];
    $salaire = $_POST['salaire'];
    $poste_id = $_POST['poste_id'];

    // Gérer la photo
    $photo = 'default.png';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = time().'_'.$_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/'.$photo);
    }

    $stmt = $pdo->prepare("INSERT INTO employes (poste_id, nom, email, telephone, adresse, date_embauche, salaire, photo) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$poste_id, $nom, $email, $telephone, $adresse, $date_embauche, $salaire, $photo]);

    header("Location: liste_employes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Employé</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:20px; }
        .container { max-width: 600px; margin:auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
        h1 { text-align:center; color:#007bff; }
        form { display:flex; flex-direction:column; }
        label { margin-top:10px; }
        input, select { padding:8px; margin-top:5px; border-radius:5px; border:1px solid #ccc; }
        button { margin-top:20px; padding:10px; border:none; border-radius:5px; background:#28a745; color:white; cursor:pointer; }
        a { text-decoration:none; color:#6c757d; margin-top:10px; display:inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un Employé</h1>
        <form method="post" enctype="multipart/form-data">
            <label>Nom:</label>
            <input type="text" name="nom" required>

            <label>Email:</label>
            <input type="email" name="email">

            <label>Téléphone:</label>
            <input type="text" name="telephone">

            <label>Adresse:</label>
            <input type="text" name="adresse">

            <label>Date d'embauche:</label>
            <input type="date" name="date_embauche">

            <label>Salaire:</label>
            <input type="number" step="0.01" name="salaire">

            <label>Poste:</label>
            <select name="poste_id" required>
                <option value="">-- Choisir un poste --</option>
                <?php foreach ($posts as $post): ?>
                    <option value="<?= $post['id'] ?>"><?= htmlspecialchars($post['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Photo:</label>
            <input type="file" name="photo">

            <button type="submit">Ajouter</button>
        </form>
        <a href="liste_employes.php">⬅ Retour à la liste</a>
    </div>
</body>
</html>
