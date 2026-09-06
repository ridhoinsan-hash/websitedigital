<?php
require_once 'includes/auth.php';
requireRole('admin');

$pesan = '';
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'sukses_tambah') $pesan = '<div class="alert alert-success">Pengeluaran berhasil dicatat!</div>';
    if ($_GET['pesan'] === 'sukses_hapus')  $pesan = '<div class="alert alert-success">Pengeluaran berhasil dihapus!</div>';
}

$total_pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS jml FROM pengeluaran"))['jml'];
$result = mysqli_query($conn, "SELECT * FROM pengeluaran ORDER BY tanggal DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pengeluaran - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Pengeluaran</h1>
                <p>Catat dan pantau biaya operasional toko</p>
            </div>
            <a href="tambah_pengeluaran.php" class="btn btn-danger">+ Catat Pengeluaran</a>
        </header>

        <div class="stats-grid" style="margin-bottom:20px;">
            <div class="stat-card red">
                <h3>Total Pengeluaran</h3>
                <div class="value" style="font-size:1.3rem;"><?= formatRupiah($total_pengeluaran); ?></div>
            </div>
        </div>

        <div class="content">
            <?= $pesan; ?>

            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td><span class="stok-badge" style="background:#fee2e2;color:#991b1b;"><?= htmlspecialchars($row['kategori']); ?></span></td>
                            <td><?= htmlspecialchars($row['keterangan']); ?></td>
                            <td style="color:#ef4444; font-weight:600;"><?= formatRupiah($row['jumlah']); ?></td>
                            <td>
                                <a href="hapus_pengeluaran.php?id=<?= $row['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return konfirmasiHapusPengeluaran(<?= $row['id']; ?>)">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">💸</div>
                <h3>Belum ada data pengeluaran</h3>
                <a href="tambah_pengeluaran.php" class="btn btn-primary" style="margin-top:12px;">+ Catat Pengeluaran</a>
            </div>
            <?php endif; ?>
        </div>

        <footer class="app-footer">Digital Electronic Ridho &copy; <?= date('Y'); ?></footer>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
