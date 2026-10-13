<?php
// post.php - Single Article Reading View with Likes & Comments
session_start();
require_once __DIR__ . '/config/database.php';

$post_id = intval($_GET['id'] ?? 0);

// Increment View Counter
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post_id]);

// Fetch Article Details
$stmt = $pdo->prepare("SELECT p.*, u.username as author_name, c.name as category_name 
                       FROM posts p 
                       JOIN users u ON p.author_id = u.id 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ? AND p.status = 'published'");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) { die("Article not found or pending approval."); }

// Fetch Total Likes & Check if Current User Liked
$like_count = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$like_count->execute([$post_id]);
$total_likes = $like_count->fetchColumn();

$user_liked = false;
if (isset($_SESSION['user_id'])) {
    $chk_like = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
    $chk_like->execute([$post_id, $_SESSION['user_id']]);
    $user_liked = (bool)$chk_like->fetch();
}

// Fetch All Comments for this Post
$comment_stmt = $pdo->prepare("SELECT c.*, u.username 
                               FROM comments c 
                               JOIN users u ON c.user_id = u.id 
                               WHERE c.post_id = ? 
                               ORDER BY c.created_at DESC");
$comment_stmt->execute([$post_id]);
$comments = $comment_stmt->fetchAll();

$page_title = htmlspecialchars($post['title']);
require_once __DIR__ . '/includes/header.php';
?>

<article style="max-width:800px; margin:0 auto;">
    <span class="badge badge-author" style="margin-bottom:15px;"><?= htmlspecialchars($post['category_name']) ?></span>
    <h1 style="font-size:2.4em; margin-bottom:15px; line-height:1.3;"><?= htmlspecialchars($post['title']) ?></h1>
    <div style="color:#94a3b8; font-size:0.9em; margin-bottom:25px; border-bottom:1px solid #1e293b; padding-bottom:15px;">
        Written by <strong style="color:#fff;"><?= htmlspecialchars($post['author_name']) ?></strong> on <?= date('F d, Y', strtotime($post['created_at'])) ?> &bull; 👁️ <?= $post['views'] ?> views
    </div>

    <img src="uploads/covers/<?= htmlspecialchars($post['cover_image']) ?>" style="width:100%; max-height:400px; object-fit:cover; border-radius:10px; margin-bottom:30px;" onerror="this.src='https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format&fit=crop';">

    <div style="font-size:1.1em; line-height:1.8; color:#cbd5e1; margin-bottom:40px;">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>

    <!-- LIKE BUTTON COMPONENT -->
    <div style="display:flex; align-items:center; gap:15px; border-top:1px solid #1e293b; border-bottom:1px solid #1e293b; padding:20px 0; margin-bottom:40px;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="process_like.php" method="POST">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" class="btn <?= $user_liked ? 'btn-danger' : 'btn-outline' ?>" style="display:flex; align-items:center; gap:8px;">
                    <?= $user_liked ? '❤️ Liked' : '🤍 Like Article' ?> (<?= $total_likes ?>)
                </button>
            </form>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline">Sign in to Like (<?= $total_likes ?>)</a>
        <?php endif; ?>
    </div>

    <!-- COMMENTS STREAM -->
    <section>
        <h3 style="margin-bottom:20px;">💬 Discussion (<?= count($comments) ?>)</h3>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="process_comment.php" method="POST" style="margin-bottom:30px;">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <textarea name="comment_text" required rows="3" placeholder="Share your thoughts on this article..." style="width:100%; padding:12px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff; margin-bottom:10px;"></textarea>
                <button type="submit" class="btn btn-primary">Submit Comment</button>
            </form>
        <?php else: ?>
            <p style="color:#94a3b8; margin-bottom:25px;"><a href="login.php">Sign in</a> to leave a comment.</p>
        <?php endif; ?>

        <div style="display:flex; flex-direction:column; gap:15px;">
            <?php foreach ($comments as $comment): ?>
                <div style="background:#0f172a; border:1px solid #1e293b; padding:15px 20px; border-radius:8px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.85em;">
                        <strong style="color:#fff;"><?= htmlspecialchars($comment['username']) ?></strong>
                        <span style="color:#64748b;"><?= date('M d, Y H:i', strtotime($comment['created_at'])) ?></span>
                    </div>
                    <p style="margin:0; color:#cbd5e1; font-size:0.95em;"><?= htmlspecialchars($comment['comment_text']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>