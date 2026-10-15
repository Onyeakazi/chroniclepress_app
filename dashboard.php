<?php
// dashboard.php - Admin / Author Analytics Dashboard
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Fetch Analytics Stat Totals
$total_posts     = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$published_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$pending_posts   = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'pending'")->fetchColumn();
$total_comments  = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$total_likes     = $pdo->query("SELECT COUNT(*) FROM likes")->fetchColumn();

// Fetch My Own Articles
$my_posts_stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM posts p JOIN categories c ON p.category_id = c.id WHERE p.author_id = ? ORDER BY p.id DESC");
$my_posts_stmt->execute([$user_id]);
$my_posts = $my_posts_stmt->fetchAll();

$page_title = "Analytics Dashboard";
require_once __DIR__ . '/includes/header.php';
?>

<h1 style="margin-bottom:10px;">📊 Platform Analytics Dashboard</h1>
<p style="color:#94a3b8; margin-bottom:20px;">Logged in as <strong style="color:var(--accent);"><?= strtoupper($role) ?></strong></p>

<?php if (isset($_GET['msg'])): ?>
    <div style="background:rgba(16,185,129,0.15); border:1px solid #10b981; color:#10b981; padding:12px 20px; border-radius:8px; margin-bottom:25px; font-weight:600;">
        ✓ <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div style="background:rgba(239,68,68,0.15); border:1px solid #ef4444; color:#ef4444; padding:12px 20px; border-radius:8px; margin-bottom:25px; font-weight:600;">
        ⚠️ <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<!-- STAT CARDS GRID -->
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom:40px;">
    <div class="card" style="padding:20px; border-left:4px solid var(--accent);">
        <span style="font-size:0.8em; color:#94a3b8; font-weight:700;">TOTAL POSTS</span>
        <h2 style="margin:10px 0 0 0; border:none;"><?= $total_posts ?></h2>
    </div>
    <div class="card" style="padding:20px; border-left:4px solid var(--success);">
        <span style="font-size:0.8em; color:#94a3b8; font-weight:700;">PUBLISHED</span>
        <h2 style="margin:10px 0 0 0; border:none;"><?= $published_posts ?></h2>
    </div>
    <div class="card" style="padding:20px; border-left:4px solid var(--danger);">
        <span style="font-size:0.8em; color:#94a3b8; font-weight:700;">PENDING REVIEW</span>
        <h2 style="margin:10px 0 0 0; border:none;"><?= $pending_posts ?></h2>
    </div>
    <div class="card" style="padding:20px; border-left:4px solid var(--warning);">
        <span style="font-size:0.8em; color:#94a3b8; font-weight:700;">COMMENTS</span>
        <h2 style="margin:10px 0 0 0; border:none;"><?= $total_comments ?></h2>
    </div>
    <div class="card" style="padding:20px; border-left:4px solid #ec4899;">
        <span style="font-size:0.8em; color:#94a3b8; font-weight:700;">TOTAL LIKES</span>
        <h2 style="margin:10px 0 0 0; border:none;"><?= $total_likes ?></h2>
    </div>
</div>

<!-- MY ARTICLES TABLE -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
    <h3 style="margin:0;">📝 My Articles</h3>
    <a href="create_post.php" class="btn btn-primary">+ Create New Article</a>
</div>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Views</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($my_posts as $p): ?>
            <tr>
                <td><strong style="color:#fff;"><?= htmlspecialchars($p['title']) ?></strong></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td>
                    <span class="badge <?= $p['status'] === 'published' ? 'badge-author' : 'badge-pending' ?>">
                        <?= strtoupper($p['status']) ?>
                    </span>
                </td>
                <td><?= $p['views'] ?></td>
                <td style="display:flex; gap:8px;">
                    <a href="edit_post.php?id=<?= $p['id'] ?>" class="btn btn-outline" style="padding:3px 8px; font-size:0.8em;">Edit</a>
                    <a href="delete_post.php?id=<?= $p['id'] ?>" class="btn btn-danger" style="padding:3px 8px; font-size:0.8em;" onclick="return confirm('Delete this post?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>