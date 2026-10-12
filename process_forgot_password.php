<?php
// process_forgot_password.php - Generate Reset Token & Link Handler
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: forgot_password.php"); exit; }

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    header("Location: forgot_password.php?error=" . urlencode("Please enter your email address."));
    exit;
}

// Verify email exists in database
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: forgot_password.php?error=" . urlencode("No account found with that email address."));
    exit;
}

// Generate secure random 64-character token
$token = bin2hex(random_bytes(32));

// Save token and 1-hour expiration timestamp in database
$stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
$stmt->execute([$token, $user['id']]);

$reset_url = "reset_password.php?token=" . $token;
$page_title = "Password Reset Link Generated";
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width: 550px; margin: 40px auto; text-align: center;">
    <div style="font-size: 3rem; margin-bottom: 10px;">🔑</div>
    <h2 style="margin-bottom:10px; border:none; color:#10b981;">Password Reset Link Ready</h2>
    <p style="margin-bottom:20px; color:#94a3b8;">
        A password reset token was successfully generated for <strong><?= htmlspecialchars($email) ?></strong>.
    </p>

    <div style="background:#0b0f19; border:1px solid #1e293b; border-radius:8px; padding:20px; text-align:left; margin-bottom:20px;">
        <span style="color:#f59e0b; font-size:0.8em; font-weight:700; text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:8px;">💡 Local Environment Link Simulation:</span>
        <p style="font-size:0.88em; color:#cbd5e1; margin-bottom:12px;">In a production app, this link is sent to the user via SMTP. For local testing, click the link below:</p>
        <a href="<?= $reset_url ?>" class="btn btn-primary" style="display:block; text-align:center; padding:10px; font-weight:600; text-decoration:none;">
            Reset Password Now &rarr;
        </a>
    </div>

    <a href="login.php" style="color:#94a3b8; font-size:0.9em;">Return to Sign In</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>