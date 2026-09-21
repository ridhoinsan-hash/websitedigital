<?php
require_once 'includes/config.php';
require_once 'includes/captcha.php';

$error = '';
$success = '';
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $captcha  = trim($_POST['captcha'] ?? '');

    if ($username === '' || $email === '') {
        $error = 'Username dan email wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (!verifyCaptcha($captcha)) {
        $error = 'CAPTCHA salah atau kedaluwarsa!';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, email FROM users WHERE username = ? AND email = ? AND status = 'aktif' LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$user) {
            $error = 'Username dan email tidak cocok / akun tidak ditemukan.';
        } else {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 jam

            mysqli_query($conn, "UPDATE password_resets SET used = 1 WHERE user_id = " . (int)$user['id'] . " AND used = 0");

            $stmt = mysqli_prepare($conn, "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iss", $user['id'], $token, $expires);
            if (mysqli_stmt_execute($stmt)) {
                $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
                $resetLink = $base . '/reset_password.php?token=' . urlencode($token);
                $success = 'Verifikasi berhasil! Gunakan link di bawah untuk mengganti password (berlaku 1 jam).';
            } else {
                $error = 'Gagal membuat token reset. Pastikan tabel password_resets sudah dibuat.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Digital Electronic</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; }
        .box { background: #1e293b; border-radius: 16px; padding: 32px; width: 100%; max-width: 420px; color: #e2e8f0; }
        .box h1 { margin: 0 0 8px; font-size: 1.5rem; }
        .box p.sub { color: #94a3b8; margin-bottom: 20px; font-size: 0.9rem; }
        .field { margin-bottom: 14px; }
        .field label { display: block; margin-bottom: 6px; font-size: 0.85rem; color: #94a3b8; }
        .field input { width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #334155; background: #0f172a; color: #fff; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 10px; background: linear-gradient(135deg,#6366f1,#8b5cf6); color: #fff; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 14px; font-size: 0.9rem; }
        .alert-danger { background: #7f1d1d; color: #fecaca; }
        .alert-success { background: #14532d; color: #bbf7d0; }
        .link-box { background: #0f172a; border: 1px dashed #475569; padding: 12px; border-radius: 8px; word-break: break-all; font-size: 0.8rem; margin-top: 10px; }
        .back { display: block; text-align: center; margin-top: 16px; color: #a5b4fc; text-decoration: none; font-size: 0.9rem; }
        .captcha-row { display: flex; gap: 8px; align-items: center; }
        .captcha-row img { height: 44px; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>
<div class="box">
    <h1>Lupa Password</h1>
    <p class="sub">Masukkan username dan email yang terdaftar untuk reset password.</p>

    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php if ($resetLink): ?>
            <div class="link-box">
                <strong>Link reset:</strong><br>
                <a href="<?= htmlspecialchars($resetLink); ?>" style="color:#93c5fd;"><?= htmlspecialchars($resetLink); ?></a>
            </div>
            <p style="font-size:0.75rem;color:#94a3b8;margin-top:8px;">*Di project sekolah link ditampilkan di sini. Di production biasanya dikirim lewat email.</p>
        <?php endif; ?>
    <?php else: ?>
    <form method="POST">
        <div class="field">
            <label>Username</label>
            <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>" autofocus>
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="field">
            <label>CAPTCHA</label>
            <div class="captcha-row">
                <img src="captcha_image.php?t=<?= time(); ?>" alt="CAPTCHA" id="captchaImg" onclick="refreshCaptcha()">
                <input type="text" name="captcha" required maxlength="8" style="flex:1;text-transform:uppercase;" autocomplete="off">
            </div>
        </div>
        <button type="submit" class="btn">Kirim Link Reset</button>
    </form>
    <?php endif; ?>

    <a class="back" href="login.php">← Kembali ke Login</a>
</div>
<script>
function refreshCaptcha() {
    document.getElementById('captchaImg').src = 'captcha_image.php?t=' + Date.now();
}
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
