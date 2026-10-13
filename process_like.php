<?php
// process_like.php - Like/Unlike Action Handler
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id'] ?? 0);

// Check if user already liked this post
$stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);
$existing = $stmt->fetch();

if ($existing) {
    // Already liked -> Toggle to UNLIKE
    $del = $pdo->prepare("DELETE FROM likes WHERE id = ?");
    $del->execute([$existing['id']]);
} else {
    // Not liked -> Toggle to LIKE
    $ins = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $ins->execute([$post_id, $user_id]);
}

header("Location: post.php?id={$post_id}");
exit;
?>