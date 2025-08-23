<?php
session_start();

// Connexion à la base de données
$host = "localhost";
$dbname = "hris_project";
$user = "root";
$pass = "Mysql";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (!$email || !$mot_de_passe) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Récupérer l'utilisateur
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - HRIS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .container { background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); width:350px; }
        h2 { text-align:center; margin-bottom:20px; }
        input { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px; }
        button { width:100%; padding:10px; background:#16a085; color:#fff; border:none; border-radius:5px; cursor:pointer; font-size:16px; }
        button:hover { background:#13876c; }
        .error { color:red; margin-bottom:10px; text-align:center; }
        .register-link { text-align:center; margin-top:10px; }
        .register-link a { text-decoration:none; color:#16a085; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>
        <?php if(!empty($error)) echo "<div class='error'>{$error}</div>"; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <div class="register-link">
            Pas de compte ? <a href="register.php">Créer un compte</a>
        </div>
    </div>
</body>
</html>
