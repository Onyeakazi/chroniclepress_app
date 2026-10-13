<?php
// index.php - Public Blog Homepage
session_start();
require_once __DIR__ . '/config/database.php';

$category_filter = $_GET['cat'] ?? '';
$search_filter   = trim($_GET['search'] ?? '');

$sql = "SELECT p.*, u.username as author_name, c.name as category_name,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
        FROM posts p
        JOIN users u ON p.author_id = u.id
        JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'published'";

$params = [];
if (!empty($category_filter)) {
    $sql .= " AND c.slug = :cat";
    $params[':cat'] = $category_filter;
}
if (!empty($search_filter)) {
    $sql .= " AND (p.title LIKE :search OR p.content LIKE :search)";
    $params[':search'] = "%{$search_filter}%";
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Fetch all categories for filter buttons
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

$page_title = "ChroniclePress — Home Blog";
require_once __DIR__ . '/includes/header.php';
?>

<div style="text-align:center; padding: 40px 0 20px 0;">
    <h1 style="font-size:2.5em; margin-bottom:10px;">Insightful Tech & Engineering Articles</h1>
    <p style="color:#94a3b8;">Discover stories, tutorials, and insights written by developers.</p>
</div>

<!-- CATEGORY BADGES & SEARCH FORM -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:30px;">
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="index.php" class="btn btn-outline" style="padding:4px 12px; font-size:0.8em; <?= empty($category_filter) ? 'border-color:var(--accent); color:#fff;' : '' ?>">All Posts</a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?cat=<?= $cat['slug'] ?>" class="btn btn-outline" style="padding:4px 12px; font-size:0.8em; <?= $category_filter === $cat['slug'] ? 'border-color:var(--accent); color:#fff;' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a>
        <?php endforeach; ?>
    </div>

    <form action="index.php" method="GET" style="display:flex; gap:8px;">
        <input type="text" name="search" value="<?= htmlspecialchars($search_filter) ?>" placeholder="Search articles..." style="padding:6px 12px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff; font-size:0.85em;">
        <button type="submit" class="btn btn-primary" style="padding:6px 12px;">Search</button>
    </form>
</div>

<!-- POSTS GRID -->
<div class="grid">
    <?php if (empty($posts)): ?>
        <div class="card" style="grid-column: 1/-1; text-align:center; padding:50px;">
            <h3 style="color:#94a3b8;">No published articles found matching your criteria.</h3>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <img src="uploads/covers/<?= htmlspecialchars($post['cover_image']) ?>" class="post-img" alt="Cover" onerror="this.src='https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop';">
                <div class="post-body">
                    <span class="badge badge-author" style="margin-bottom:10px;"><?= htmlspecialchars($post['category_name']) ?></span>
                    <h3 style="font-size:1.2em; margin-top:5px; margin-bottom:10px;"><a href="post.php?id=<?= $post['id'] ?>" style="color:#fff;"><?= htmlspecialchars($post['title']) ?></a></h3>
                    <p style="font-size:0.88em; color:#94a3b8; margin-bottom:15px;"><?= htmlspecialchars(substr(strip_tags($post['content']), 0, 100)) ?>...</p>
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8em; color:#64748b; border-top:1px solid #1e293b; padding-top:10px;">
                        <span>By <strong style="color:#cbd5e1;"><?= htmlspecialchars($post['author_name']) ?></strong></span>
                        <span>❤️ <?= $post['like_count'] ?> | 💬 <?= $post['comment_count'] ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>