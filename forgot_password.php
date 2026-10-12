<?php
// forgot_password.php - Request Password Reset Form
session_start();
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
$page_title = "Forgot Password";
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width: 450px; margin: 40px auto;">
    <h2 style="margin-bottom:10px; border:none;">Reset Password</h2>
    <p style="margin-bottom:20px;">Enter your account email address. We will generate a secure reset link for your account.</p>

    <?php if (isset($_GET['error'])): ?>
        <div style="background:rgba(239,68,68,0.2); color:#ef4444; padding:10px; border-radius:6px; margin-bottom:15px; font-size:0.9em;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="process_forgot_password.php" method="POST">
        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">EMAIL ADDRESS</label>
            <input type="email" name="email" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;" placeholder="you@example.com">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; padding:12px;">Send Password Reset Link</button>
    </form>
    <div style="margin-top:15px; text-align:center; font-size:0.85em;">
        <a href="login.php" style="color:#94a3b8;">&larr; Back to Sign In</a>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>