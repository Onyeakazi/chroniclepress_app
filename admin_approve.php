<?php
// admin_approve.php - Super Admin Post Approval Queue
session_start();
require_once __DIR__ . '/config/database.php';

// STRICT SUPER ADMIN GUARD
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// HANDLE APPROVAL / REJECTION ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id'] ?? 0);
    $action  = $_POST['action'] ?? '';

    if ($action === 'approve') {
        $pdo->prepare("UPDATE posts SET status = 'published' WHERE id = ?")->execute([$post_id]);
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE posts SET status = 'draft' WHERE id = ?")->execute([$post_id]);
    }
    header("Location: admin_approve.php?msg=" . urlencode("Status updated!"));
    exit;
}

// FETCH ALL PENDING POSTS FOR REVIEW
$stmt = $pdo->query("SELECT p.*, u.username as author_name, c.name as category_name 
                    FROM posts p 
                    JOIN users u ON p.author_id = u.id 
                    JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = 'pending' 
                    ORDER BY p.id ASC");
$pending_posts = $stmt->fetchAll();

$page_title = "Moderation Queue";
require_once __DIR__ . '/includes/header.php';
?>

<h1 style="margin-bottom:10px;">🛡️ Super Admin Moderation Queue</h1>
<p style="color:#94a3b8; margin-bottom:30px;">Review articles submitted by authors. Approved posts appear live on the public blog.</p>

<?php if (empty($pending_posts)): ?>
    <div class="card" style="text-align:center; padding:40px;">
        <h3 style="color:#10b981;">✅ Moderation Queue is Empty!</h3>
        <p>All submitted articles have been reviewed.</p>
    </div>
<?php else: ?>
    <div style="display:flex; flex-direction:column; gap:20px;">
        <?php foreach ($pending_posts as $post): ?>
            <div class="card" style="border-left:4px solid var(--danger);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span class="badge badge-pending">PENDING REVIEW</span>
                    <span style="font-size:0.85em; color:#64748b;">Submitted by <strong style="color:#fff;"><?= htmlspecialchars($post['author_name']) ?></strong> (<?= htmlspecialchars($post['category_name']) ?>)</span>
                </div>

                <h2 style="font-size:1.5em; border:none; margin-bottom:10px;"><?= htmlspecialchars($post['title']) ?></h2>
                <p style="color:#cbd5e1; font-size:0.95em; margin-bottom:20px;"><?= htmlspecialchars(substr($post['content'], 0, 250)) ?>...</p>

                <div style="display:flex; gap:10px;">
                    <form action="admin_approve.php" method="POST">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-success">✓ Approve & Publish</button>
                    </form>
                    <form action="admin_approve.php" method="POST">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn btn-danger">✕ Reject to Draft</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>