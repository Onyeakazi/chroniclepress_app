<?php
// edit_post.php - Edit Article View
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$post_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Admins can edit any post; Authors can only edit their own
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?" . ($role === 'admin' ? "" : " AND author_id = $user_id"));
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) { die("Unauthorized access or post not found."); }

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$page_title = "Edit Article";
require_once __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width:700px; margin:0 auto;">
    <h2 style="margin-bottom:20px; border:none;">Edit Article</h2>

    <form action="process_edit_post.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">TITLE</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">CATEGORY</label>
            <select name="category_id" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $post['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">REPLACE COVER IMAGE (OPTIONAL)</label>
            <input type="file" name="cover" accept="image/*" style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">CONTENT</label>
            <textarea name="content" required rows="10" style="width:100%; padding:12px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;"><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="padding:12px 24px;">Save Changes</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>