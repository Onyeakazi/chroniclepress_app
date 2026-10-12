<?php
// login.php - User Login Form
session_start();
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
$page_title = "Sign In";
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width: 450px; margin: 40px auto;">
    <h2 style="margin-bottom:10px; border:none;">Welcome Back</h2>
    <p style="margin-bottom:20px;">Sign in to manage your account and articles.</p>

    <?php if (isset($_GET['msg'])): ?>
        <div style="background:rgba(16,185,129,0.2); color:#10b981; padding:10px; border-radius:6px; margin-bottom:15px; font-size:0.9em;">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div style="background:rgba(239,68,68,0.2); color:#ef4444; padding:10px; border-radius:6px; margin-bottom:15px; font-size:0.9em;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="process_login.php" method="POST">
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">EMAIL ADDRESS</label>
            <input type="email" name="email" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>
        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">PASSWORD</label>
            <input type="password" name="password" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; padding:12px;">Sign In</button>
    </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>