<?php
// delete_post.php - Deletes SQL Record and Purges Cover Image via unlink()
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$post_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?" . ($role === 'admin' ? "" : " AND author_id = $user_id"));
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if ($post) {
    // 1. Purge physical cover image file using unlink()
    if ($post['cover_image'] && $post['cover_image'] !== 'default_cover.jpg') {
        $cover_path = __DIR__ . '/uploads/covers/' . $post['cover_image'];
        if (file_exists($cover_path)) {
            unlink($cover_path);
        }
    }
    // 2. Delete database row
    $del = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $del->execute([$post_id]);
}

header("Location: dashboard.php?msg=" . urlencode("Article deleted successfully."));
exit;
?>