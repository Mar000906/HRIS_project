<?php
// database.php

$host = "localhost";       // hôte MySQL
$user = "root";            // utilisateur par défaut de XAMPP
$password = "Mysql";       // ton mot de passe MySQL
$dbname = "hris_project";  // nom de la base de données

try {
    // Création de la connexion PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);

    // Activer le mode d'erreur
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussie !"; // tu peux décommenter pour tester
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
