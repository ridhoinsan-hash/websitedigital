<?php
require_once 'includes/auth.php';
requireRole(['admin']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');
    $role         = $_POST['role'] ?? 'kasir';
    $status       = $_POST['status'] ?? 'aktif';

    if ($username === '' || $password === '' || $nama_lengkap === '') {
        $error = 'Username, password, dan nama lengkap wajib diisi!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek username unik
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $cek = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "sssssss", $username, $hash, $nama_lengkap, $email, $no_hp, $role, $status);
            if (mysqli_stmt_execute($stmt2)) {
                header("Location: karyawan.php?success=" . urlencode("Karyawan berhasil ditambahkan."));
                exit;
            } else {
                $error = 'Gagal menambahkan: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt2);
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
    <title>Tambah Karyawan - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Tambah Karyawan</h1>
                <p>Buat akun baru untuk admin, kasir, atau staff</p>
            </div>
            <a href="karyawan.php" class="btn btn-outline">← Kembali</a>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="content" style="max-width:600px;">
            <form method="POST" class="form-card">
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required
                           value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>"
                           placeholder="Contoh: Budi Santoso">
                </div>
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required
                           value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                           placeholder="Username untuk login">
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required minlength="6"
                           placeholder="Minimal 6 karakter">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                               placeholder="email@contoh.com">
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp"
                               value="<?= htmlspecialchars($_POST['no_hp'] ?? ''); ?>"
                               placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required>
                            <option value="admin">Administrator</option>
                            <option value="kasir" selected>Kasir</option>
                            <option value="staff">Staff</option>
                            <option value="pembeli">Pembeli</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="aktif" selected>Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top:20px; display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">Simpan Karyawan</button>
                    <a href="karyawan.php" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
