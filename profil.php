<?php
require_once 'includes/auth.php';
requireLogin();

$user = currentUser();
$error = '';
$success = '';

// Ambil data lengkap
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($nama === '') {
        $error = 'Nama lengkap wajib diisi!';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif ($pass !== '' && strlen($pass) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } elseif ($pass !== '' && $pass !== $pass2) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        if ($pass !== '') {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET nama_lengkap=?, email=?, no_hp=?, password=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssi", $nama, $email, $no_hp, $hash, $user['id']);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET nama_lengkap=?, email=?, no_hp=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssi", $nama, $email, $no_hp, $user['id']);
        }
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['nama_lengkap'] = $nama;
            $success = 'Profil berhasil diperbarui.';
            $data['nama_lengkap'] = $nama;
            $data['email'] = $email;
            $data['no_hp'] = $no_hp;
        } else {
            $error = 'Gagal menyimpan: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Profil Saya - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container">
        <header class="page-header">
            <div>
                <h1>Profil Saya</h1>
                <p>Kelola data akun Anda</p>
            </div>
            <a href="<?= $user['role'] === 'pembeli' ? 'produk.php' : 'index.php'; ?>" class="btn btn-secondary">← Kembali</a>
        </header>

        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success); ?></div><?php endif; ?>

        <div class="content" style="max-width:560px;">
            <form method="POST" class="form-card">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?= htmlspecialchars($data['username']); ?>" disabled>
                    <small style="color:#94a3b8;">Username tidak dapat diubah</small>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="<?= roleLabel($data['role']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required
                           value="<?= htmlspecialchars($data['nama_lengkap']); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="no_hp">No. HP</label>
                    <input type="text" id="no_hp" name="no_hp"
                           value="<?= htmlspecialchars($data['no_hp'] ?? ''); ?>">
                </div>
                <hr style="border:none;border-top:1px solid #e2e8f0;margin:18px 0;">
                <p style="font-size:0.85rem;color:#64748b;margin-bottom:12px;">Ubah password (opsional — kosongkan jika tidak diganti)</p>
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" minlength="6" placeholder="Min. 6 karakter">
                </div>
                <div class="form-group">
                    <label for="password2">Ulangi Password Baru</label>
                    <input type="password" id="password2" name="password2" minlength="6">
                </div>
                <button type="submit" class="btn btn-primary">Simpan Profil</button>
            </form>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
