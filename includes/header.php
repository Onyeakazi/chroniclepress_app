<?php
// includes/header.php - Dynamic Header Navigation Layout
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'ChroniclePress' ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="nav-brand">🗞️ ChroniclePress</a>
        <div class="nav-links">
            <a href="index.php">Home Blog</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <?php if (in_array($_SESSION['role'], ['author', 'admin'])): ?>
                    <a href="create_post.php" class="btn btn-primary" style="padding:4px 12px;">+ Write Article</a>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_approve.php" class="badge badge-admin">Queue</a>
                    <a href="admin_comments.php" class="badge badge-admin">Comments</a>
                <?php endif; ?>
                <span style="color:#94a3b8; font-size:0.85em;">Hi, <strong style="color:#fff;"><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="logout.php" class="btn btn-outline" style="padding:4px 10px;">Logout</a>
            <?php else: ?>
                <a href="login.php">Sign In</a>
                <a href="register.php" class="btn btn-primary" style="padding:4px 12px;">Register</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container">