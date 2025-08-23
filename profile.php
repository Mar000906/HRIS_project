<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connexion PDO
$host = "localhost";
$dbname = "hris_project";
$user = "root";
$pass = "Mysql";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base : ".$e->getMessage());
}

// Récupérer les infos actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id'=>$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$message = "";

// Gestion du formulaire
if(isset($_POST['update_profile'])){
    $nom = $_POST['nom'];
    $email = $_POST['email'];

    // Vérifier si une photo a été uploadée
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
        $uploadDir = 'uploads/';
        // Créer le dossier s'il n'existe pas
        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0755, true);
        }

        // Renommer le fichier pour éviter collision
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['photo']['name'], PATHINFO_FILENAME)) . '.' . $ext;
        $uploadFile = $uploadDir . $filename;

        if(move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)){
            // Supprimer l'ancienne photo si elle existe
            if(!empty($user['photo_profil']) && file_exists($uploadDir.$user['photo_profil'])){
                unlink($uploadDir.$user['photo_profil']);
            }
            // Mettre à jour la base avec la nouvelle photo
            $stmt = $pdo->prepare("UPDATE users SET nom=:nom, email=:email, photo_profil=:photo WHERE id=:id");
            $stmt->execute([
                'nom'=>$nom,
                'email'=>$email,
                'photo'=>$filename,
                'id'=>$_SESSION['user_id']
            ]);
            $message = "Profil mis à jour avec succès !";
        } else {
            $message = "Erreur lors du téléchargement de la photo.";
        }
    } else {
        // Mise à jour sans changer la photo
        $stmt = $pdo->prepare("UPDATE users SET nom=:nom, email=:email WHERE id=:id");
        $stmt->execute([
            'nom'=>$nom,
            'email'=>$email,
            'id'=>$_SESSION['user_id']
        ]);
        $message = "Profil mis à jour avec succès !";
    }

    // Rafraîchir les infos utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id'=>$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Profil</title>
<style>
body { font-family: Arial, sans-serif; background:#181824; color:#fff; margin:0; padding:20px; }
.container { max-width:500px; margin:50px auto; background:#2c2a3c; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.3); }
h2 { text-align:center; margin-bottom:20px; }
form { display:flex; flex-direction:column; gap:15px; }
input[type=text], input[type=email], input[type=file] { padding:10px; border-radius:6px; border:none; width:100%; }
input[type=submit] { padding:12px; border:none; border-radius:6px; background:#3498db; color:#fff; cursor:pointer; font-weight:bold; transition:0.3s; }
input[type=submit]:hover { background:#2980b9; }
.profile-img { text-align:center; margin-bottom:15px; }
.profile-img img { width:100px; height:100px; border-radius:50%; border:2px solid #fff; object-fit:cover; }
.message { background:#2ecc71; padding:10px; border-radius:6px; text-align:center; margin-bottom:15px; color:#fff; }
</style>
</head>
<body>

<div class="container">
    <h2>Modifier Profil</h2>
    <?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="profile-img">
            <img src="uploads/<?= htmlspecialchars($user['photo_profil'] ?? 'default.png') ?>" alt="Avatar">
        </div>
        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" placeholder="Nom" required>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required>
        <input type="file" name="photo" accept="image/*">
        <input type="submit" name="update_profile" value="Mettre à jour le profil">
    </form>
</div>

</body>
</html>
