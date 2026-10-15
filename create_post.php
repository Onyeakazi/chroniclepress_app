<?php
// create_post.php - Create Article View
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['author', 'admin'])) {
    header("Location: index.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$page_title = "Create New Article";
require_once __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width:700px; margin:0 auto;">
    <h2 style="margin-bottom:10px; border:none;">Create New Article</h2>
    <p style="margin-bottom:20px; color:#94a3b8;">
        <?= $_SESSION['role'] === 'admin' ? 'Admins publish immediately.' : 'Authors submit articles for Admin Approval.' ?>
    </p>

    <?php if (isset($_GET['error'])): ?>
        <div style="background:rgba(239,68,68,0.15); border:1px solid #ef4444; color:#ef4444; padding:12px 20px; border-radius:8px; margin-bottom:20px; font-weight:600;">
            ⚠️ <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="process_post.php" method="POST" enctype="multipart/form-data">
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">ARTICLE TITLE</label>
            <input type="text" name="title" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">CATEGORY</label>
            <select name="category_id" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">COVER IMAGE</label>
            <input type="file" name="cover" accept="image/*" style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">ARTICLE CONTENT</label>
            <textarea name="content" required rows="10" style="width:100%; padding:12px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;"></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="padding:12px 24px;">Submit Article</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>