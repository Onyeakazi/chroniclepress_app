<?php
// register.php - User Signup View
session_start();
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
$page_title = "Create Account";
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width: 450px; margin: 40px auto;">
    <h2 style="margin-bottom:10px; border:none;">Create Account</h2>
    <p style="margin-bottom:20px;">Join ChroniclePress to read, like, and comment.</p>

    <?php if (isset($_GET['error'])): ?>
        <div style="background:rgba(239,68,68,0.2); color:#ef4444; padding:10px; border-radius:6px; margin-bottom:15px; font-size:0.9em;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="process_register.php" method="POST">
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">USERNAME</label>
            <input type="text" name="username" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">EMAIL ADDRESS</label>
            <input type="email" name="email" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">PASSWORD</label>
            <input type="password" name="password" required style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
        </div>
        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:5px; color:#94a3b8; font-size:0.85em;">REGISTER AS</label>
            <select name="role" style="width:100%; padding:10px; background:#0b0f19; border:1px solid #1e293b; border-radius:6px; color:#fff;">
                <option value="subscriber">Reader / Subscriber (Read, Like & Comment)</option>
                <option value="author">Blog Author (Write Articles for Review)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; padding:12px;">Register Account</button>
    </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>