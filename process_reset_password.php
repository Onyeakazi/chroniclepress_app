<?php
// process_reset_password.php - Update Password Processor
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: forgot_password.php"); exit; }

$token            = trim($_POST['token'] ?? '');
$password         = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($token) || empty($password) || empty($confirm_password)) {
    header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("All fields are required."));
    exit;
}

if ($password !== $confirm_password) {
    header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("Passwords do not match."));
    exit;
}

if (strlen($password) < 6) {
    header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("Password must be at least 6 characters long."));
    exit;
}

// Verify token in database
$stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: forgot_password.php?error=" . urlencode("Invalid or expired reset token. Please try again."));
    exit;
}

// Hash new password and clear reset token & expiration
$new_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
$stmt->execute([$new_hash, $user['id']]);

header("Location: login.php?msg=" . urlencode("Password reset successful! Please log in with your new password."));
exit;
?>