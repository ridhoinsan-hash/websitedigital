<?php
require_once 'includes/auth.php';
requireRole(['admin']);

$id = (int)($_GET['id'] ?? 0);
if ($id < 1) {
    header("Location: karyawan.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    header("Location: karyawan.php?error=" . urlencode("Data tidak ditemukan."));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');
    $role         = $_POST['role'] ?? 'kasir';
    $status       = $_POST['status'] ?? 'aktif';

    if ($username === '' || $nama_lengkap === '') {
        $error = 'Username dan nama lengkap wajib diisi!';
    } else {
        // Cek username unik (kecuali diri sendiri)
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, "si", $username, $id);
        mysqli_stmt_execute($stmt);
        $cek = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            if ($password !== '') {
                if (strlen($password) < 6) {
                    $error = 'Password minimal 6 karakter!';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt2 = mysqli_prepare($conn, "UPDATE users SET username=?, password=?, nama_lengkap=?, email=?, no_hp=?, role=?, status=? WHERE id=?");
                    mysqli_stmt_bind_param($stmt2, "sssssssi", $username, $hash, $nama_lengkap, $email, $no_hp, $role, $status, $id);
                }
            } else {
                $stmt2 = mysqli_prepare($conn, "UPDATE users SET username=?, nama_lengkap=?, email=?, no_hp=?, role=?, status=? WHERE id=?");
                mysqli_stmt_bind_param($stmt2, "ssssssi", $username, $nama_lengkap, $email, $no_hp, $role, $status, $id);
            }

            if (empty($error) && isset($stmt2) && mysqli_stmt_execute($stmt2)) {
                // Update session jika edit diri sendiri
                if ($id == currentUser()['id']) {
                    $_SESSION['username'] = $username;
                    $_SESSION['nama_lengkap'] = $nama_lengkap;
                    $_SESSION['role'] = $role;
                }
                header("Location: karyawan.php?success=" . urlencode("Data karyawan berhasil diperbarui."));
                exit;
            } elseif (empty($error)) {
                $error = 'Gagal memperbarui: ' . mysqli_error($conn);
            }
            if (isset($stmt2)) mysqli_stmt_close($stmt2);
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
    <title>Edit Karyawan - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Edit Karyawan</h1>
                <p>Perbarui data akun karyawan</p>
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
                           value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? $user['nama_lengkap']); ?>">
                </div>
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required
                           value="<?= htmlspecialchars($_POST['username'] ?? $user['username']); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password" minlength="6"
                           placeholder="Kosongkan jika tidak diubah">
                    <small style="color:#64748b;">Biarkan kosong jika tidak ingin mengubah password</small>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? $user['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp"
                               value="<?= htmlspecialchars($_POST['no_hp'] ?? $user['no_hp'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select id="role" name="role" required>
                            <?php
                            $roles = ['admin' => 'Administrator', 'kasir' => 'Kasir', 'staff' => 'Staff', 'pembeli' => 'Pembeli'];
                            $curRole = $_POST['role'] ?? $user['role'];
                            foreach ($roles as $val => $label):
                            ?>
                            <option value="<?= $val; ?>" <?= $curRole === $val ? 'selected' : ''; ?>><?= $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <?php $curStatus = $_POST['status'] ?? $user['status']; ?>
                            <option value="aktif" <?= $curStatus === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="nonaktif" <?= $curStatus === 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top:20px; display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="karyawan.php" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
