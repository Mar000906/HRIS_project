<?php
$host = "localhost";
$dbname = "hris_project";  // ⚠️ nom exact de ta base
$username = "root";        // par défaut sous XAMPP
$password = "Mysql";            // vide par défaut sous XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
