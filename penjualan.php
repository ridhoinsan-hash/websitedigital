<?php
require_once 'includes/auth.php';
requireRole(['admin', 'kasir']);

$pesan = '';
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'sukses_tambah') $pesan = '<div class="alert alert-success">Transaksi berhasil disimpan! Stok telah dikurangi.</div>';
    if ($_GET['pesan'] === 'sukses_hapus')  $pesan = '<div class="alert alert-success">Transaksi dihapus. Stok telah dikembalikan.</div>';
}

$result = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap 
    FROM penjualan p 
    LEFT JOIN users u ON p.user_id = u.id 
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Penjualan - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Daftar Penjualan</h1>
                <p>Riwayat transaksi & checkout toko</p>
            </div>
            <a href="tambah_penjualan.php" class="btn btn-success">+ Checkout Baru</a>
        </header>

        <div class="content">
            <?= $pesan; ?>

            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong>#<?= $row['id']; ?></strong></td>
                            <td><?= date('d M Y H:i', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
                            <td class="harga"><?= formatRupiah($row['total']); ?></td>
                            <td><?= htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                            <td>
                                <a href="detail_penjualan.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Detail</a>
                                <a href="hapus_penjualan.php?id=<?= $row['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return konfirmasiHapusPenjualan(<?= $row['id']; ?>)">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">🛒</div>
                <h3>Belum ada transaksi</h3>
                <p>Mulai dengan membuat checkout pertama</p>
                <a href="tambah_penjualan.php" class="btn btn-primary" style="margin-top:12px;">+ Checkout Baru</a>
            </div>
            <?php endif; ?>
        </div>

        <footer class="app-footer">Digital Electronic Ridho &copy; <?= date('Y'); ?></footer>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
