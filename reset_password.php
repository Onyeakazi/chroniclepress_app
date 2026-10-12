<?php
// reset_password.php - New Password Entry Form
session_start();
require_once __DIR__ . '/config/database.php';

$token = trim($_GET['token'] ?? '');
$valid = false;
$user = null;

if (!empty($token)) {
    // Validate token and check expiration
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if ($user) {
        $valid = true;
    }
}

$page_title = "Choose New Password";
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width: 450px; margin: 40px auto;">
    <?php if (!$valid): ?>
        <div style="text-align:center; padding:10px;">
            <div style="font-size:2.5rem; margin-bottom:10px;">⚠️</div>
            <h2 style="margin-bottom:10px; border:none; color:#ef4444;">Invalid or Expired Link</h2>
            <p style="color:#94a3b8; margin-bottom:20px;">This password reset token is invalid or has expired (reset links expire after 1 hour).</p>
            <a href="forgot_password.php" class="btn btn-primary">Request New Reset Link</a>
        </div>
    <?php else: ?>
        <h2 style="margin-bottom:10px; border:none;">Reset Your Password</h2>
        <p style="margin-bottom:20px; font-size:0.9em; color:#94a3b8;">Create a new secure password for <strong><?= htmlspecialchars($user['email']) ?></strong>.</p>

        <?php if (isset($_GET['error'])): ?>
            <div style="background:rgba(239,68,68,0.2); color:#ef4444; padding:10px; border-radius:6px; margin-bottom:15px; font-size:0.9em;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="process_reset_password.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">NEW PASSWORD</label>
                <input type="password" name="password" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;" placeholder="At least 6 characters">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">CONFIRM NEW PASSWORD</label>
                <input type="password" name="confirm_password" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;" placeholder="Repeat new password">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding:12px;">Update Password & Sign In</button>
        </form>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>