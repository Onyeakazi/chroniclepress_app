<?php
// process_edit_post.php - Handles Post Updates & Image Replacement
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$post_id     = intval($_POST['post_id'] ?? 0);
$user_id     = $_SESSION['user_id'];
$role        = $_SESSION['role'];
$title       = trim($_POST['title'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$content     = trim($_POST['content'] ?? '');

// Fetch existing post to verify authorization
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?" . ($role === 'admin' ? "" : " AND author_id = $user_id"));
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) { die("Unauthorized edit attempt."); }

// Check if new cover image uploaded
if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($ext, $allowed)) {
        // Unlink old cover image if not default
        if ($post['cover_image'] && $post['cover_image'] !== 'default_cover.jpg') {
            $old_path = __DIR__ . '/uploads/covers/' . $post['cover_image'];
            if (file_exists($old_path)) unlink($old_path);
        }
        $new_cover = 'cover_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        move_uploaded_file($_FILES['cover']['tmp_name'], __DIR__ . '/uploads/covers/' . $new_cover);
        
        $pdo->prepare("UPDATE posts SET cover_image = ? WHERE id = ?")->execute([$new_cover, $post_id]);
    }
}

// Update title, category, and content
$update = $pdo->prepare("UPDATE posts SET title = ?, category_id = ?, content = ? WHERE id = ?");
$update->execute([$title, $category_id, $content, $post_id]);

header("Location: dashboard.php?msg=" . urlencode("Article updated successfully!"));
exit;
?>