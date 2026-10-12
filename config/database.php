<?php
// config/database.php - Pure Procedural PDO MySQL Connection
$host = 'localhost';
$db   = 'chroniclepress_db';
$user = 'root';
$pass = ''; // Default XAMPP/MAMP local environment password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("CRITICAL ERROR: Database Connection Failed: " . $e->getMessage());
}
?>