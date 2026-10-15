<?php
// admin_comments.php - Super Admin Comment Moderation Table
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Handle Comment Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment_id'])) {
    $comm_id = intval($_POST['delete_comment_id']);
    $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$comm_id]);
    header("Location: admin_comments.php?msg=" . urlencode("Comment deleted."));
    exit;
}

// Fetch All Comments with Article Titles
$comments = $pdo->query("SELECT c.*, u.username, p.title as article_title 
                        FROM comments c 
                        JOIN users u ON c.user_id = u.id 
                        JOIN posts p ON c.post_id = p.id 
                        ORDER BY c.id DESC")->fetchAll();

$page_title = "Comment Moderation";
require_once __DIR__ . '/includes/header.php';
?>

<h1 style="margin-bottom:10px;">💬 Comment Moderation Control</h1>
<p style="color:#94a3b8; margin-bottom:30px;">Monitor and purge user comments across all blog articles.</p>

<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Article</th>
            <th>Comment Text</th>
            <th>Posted Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($comments as $c): ?>
            <tr>
                <td><strong style="color:#fff;"><?= htmlspecialchars($c['username']) ?></strong></td>
                <td><a href="post.php?id=<?= $c['post_id'] ?>" target="_blank"><?= htmlspecialchars(substr($c['article_title'], 0, 30)) ?>...</a></td>
                <td><?= htmlspecialchars($c['comment_text']) ?></td>
                <td><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></td>
                <td>
                    <form action="admin_comments.php" method="POST" onsubmit="return confirm('Delete this comment?');">
                        <input type="hidden" name="delete_comment_id" value="<?= $c['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="padding:3px 8px; font-size:0.8em;">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>