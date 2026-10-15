<?php
// process_post.php - Handles Post Creation & Status Assignment
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['author', 'admin'])) {
    header("Location: index.php");
    exit;
}

$user_id     = $_SESSION['user_id'];
$role        = $_SESSION['role'];
$title       = trim($_POST['title'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$content     = trim($_POST['content'] ?? '');

// Generate clean URL slug
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-')) . '-' . time();

// Default status: Admins auto-publish; Authors submit as 'pending'
$status = ($role === 'admin') ? 'published' : 'pending';

// Handle Cover Image Upload
$cover_filename = 'default_cover.jpg';
if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($ext, $allowed)) {
        $cover_filename = 'cover_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target_dir = __DIR__ . '/uploads/covers/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        move_uploaded_file($_FILES['cover']['tmp_name'], $target_dir . $cover_filename);
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO posts (author_id, category_id, title, slug, content, cover_image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $category_id, $title, $slug, $content, $cover_filename, $status]);

    $msg = ($status === 'published') 
        ? "Article published successfully!" 
        : "Article submitted for Admin approval! (Status: Pending)";

    header("Location: dashboard.php?msg=" . urlencode($msg));
    exit;
} catch (PDOException $e) {
    header("Location: create_post.php?error=" . urlencode("Database Error: " . $e->getMessage()));
    exit;
}
?>