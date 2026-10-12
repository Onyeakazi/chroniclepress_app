<?php
// process_login.php - Login Authenticator
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: login.php"); exit; }

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role']; // 'subscriber', 'author', 'admin'
    header("Location: index.php");
    exit;
}

header("Location: login.php?error=" . urlencode("Invalid email or password."));
exit;
?>