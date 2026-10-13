<?php
// process_comment.php - Add Comment Action Handler
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$user_id      = $_SESSION['user_id'];
$post_id      = intval($_POST['post_id'] ?? 0);
$comment_text = trim($_POST['comment_text'] ?? '');

if ($post_id && !empty($comment_text)) {
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $comment_text]);
}

header("Location: post.php?id={$post_id}");
exit;
?>