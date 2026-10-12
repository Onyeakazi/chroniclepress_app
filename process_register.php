<?php
// process_register.php - Registration Handler
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: register.php"); exit; }

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = in_array($_POST['role'] ?? '', ['subscriber', 'author']) ? $_POST['role'] : 'subscriber';

if (empty($username) || empty($email) || empty($password)) {
    header("Location: register.php?error=" . urlencode("All fields are required."));
    exit;
}

// Check duplicate email or username
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->execute([$email, $username]);
if ($stmt->fetch()) {
    header("Location: register.php?error=" . urlencode("Username or email already exists."));
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $email, $hash, $role]);

header("Location: login.php?msg=" . urlencode("Registration successful! Please login."));
exit;
?>