<?php
require_once 'includes/auth.php';
requireRole(['admin']);

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Daftar Karyawan - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Daftar Karyawan</h1>
                <p>Kelola akun admin, kasir, staff, dan pembeli</p>
            </div>
            <a href="tambah_karyawan.php" class="btn btn-primary">+ Tambah Karyawan</a>
        </header>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="content">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($row['no_hp'] ?? '-'); ?></td>
                            <td>
                                <span class="badge-role <?= roleBadgeClass($row['role']); ?>">
                                    <?= roleLabel($row['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'aktif'): ?>
                                    <span style="color:#16a34a; font-weight:600;">● Aktif</span>
                                <?php else: ?>
                                    <span style="color:#dc2626; font-weight:600;">● Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="edit_karyawan.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                <?php if ($row['id'] != currentUser()['id']): ?>
                                <a href="hapus_karyawan.php?id=<?= $row['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return konfirmasiHapus('<?= htmlspecialchars(addslashes($row['nama_lengkap'])); ?>');">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="8" style="text-align:center; padding:30px;">Belum ada data karyawan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
