<?php
require_once 'includes/config.php';

$error = '';
$success = '';
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$valid = false;
$userId = 0;

if ($token !== '') {
    $stmt = mysqli_prepare($conn, "SELECT pr.*, u.username FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW() LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($row) {
        $valid = true;
        $userId = (int)$row['user_id'];
        $username = $row['username'];
    } else {
        $error = 'Link reset tidak valid atau sudah kedaluwarsa.';
    }
} else {
    $error = 'Token tidak ditemukan.';
}

if ($valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if (strlen($pass) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($pass !== $pass2) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hash, $userId);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_query($conn, "UPDATE password_resets SET used = 1 WHERE token = '" . mysqli_real_escape_string($conn, $token) . "'");
            $success = 'Password berhasil diganti! Silakan login dengan password baru.';
            $valid = false;
        } else {
            $error = 'Gagal menyimpan password.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Digital Electronic</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; }
        .box { background: #1e293b; border-radius: 16px; padding: 32px; width: 100%; max-width: 420px; color: #e2e8f0; }
        .box h1 { margin: 0 0 8px; font-size: 1.5rem; }
        .field { margin-bottom: 14px; }
        .field label { display: block; margin-bottom: 6px; font-size: 0.85rem; color: #94a3b8; }
        .field input { width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #334155; background: #0f172a; color: #fff; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 10px; background: linear-gradient(135deg,#6366f1,#8b5cf6); color: #fff; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 14px; font-size: 0.9rem; }
        .alert-danger { background: #7f1d1d; color: #fecaca; }
        .alert-success { background: #14532d; color: #bbf7d0; }
        .back { display: block; text-align: center; margin-top: 16px; color: #a5b4fc; text-decoration: none; }
    </style>
</head>
<body>
<div class="box">
    <h1>Reset Password</h1>
    <?php if ($error && !$valid && !$success): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <a class="back" href="lupa_password.php">Minta link baru</a>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <a class="back" href="login.php">→ Login sekarang</a>
    <?php elseif ($valid): ?>
        <p style="color:#94a3b8;font-size:0.9rem;">Akun: <strong><?= htmlspecialchars($username); ?></strong></p>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
            <div class="field">
                <label>Password baru</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="field">
                <label>Ulangi password</label>
                <input type="password" name="password2" required minlength="6">
            </div>
            <button type="submit" class="btn">Simpan Password Baru</button>
        </form>
    <?php endif; ?>
    <a class="back" href="login.php">← Kembali ke Login</a>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>
