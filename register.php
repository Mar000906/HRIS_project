<?php
// register.php
session_start();

// Connexion à la base de données
$host = "localhost";
$dbname = "hris_project";
$user = "root";
$pass = "Mysql"; // ton mot de passe MySQL

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Vérifier que les champs sont remplis
    if (!$nom || !$email || !$mot_de_passe) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Hasher le mot de passe
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insérer l'utilisateur
            $stmt = $conn->prepare("INSERT INTO users (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)");
            $stmt->execute([
                'nom' => $nom,
                'email' => $email,
                'mot_de_passe' => $hash
            ]);

            $_SESSION['success'] = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
            header("Location: login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte - HRIS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .container { background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); width:350px; }
        h2 { text-align:center; margin-bottom:20px; }
        input { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px; }
        button { width:100%; padding:10px; background:#16a085; color:#fff; border:none; border-radius:5px; cursor:pointer; font-size:16px; }
        button:hover { background:#13876c; }
        .error { color:red; margin-bottom:10px; text-align:center; }
        .success { color:green; margin-bottom:10px; text-align:center; }
        .login-link { text-align:center; margin-top:10px; }
        .login-link a { text-decoration:none; color:#16a085; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Créer un compte</h2>
        <?php if(!empty($error)) echo "<div class='error'>{$error}</div>"; ?>
        <?php if(!empty($_SESSION['success'])) { echo "<div class='success'>{$_SESSION['success']}</div>"; unset($_SESSION['success']); } ?>
        <form method="POST" action="">
            <input type="text" name="nom" placeholder="Nom complet" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Créer un compte</button>
        </form>
        <div class="login-link">
            Déjà un compte ? <a href="login.php">Connectez-vous</a>
        </div>
    </div>
</body>
</html>
