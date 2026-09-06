<?php
require_once 'includes/auth.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: penjualan.php");
    exit;
}

// Header
$stmt = mysqli_prepare($conn, "
    SELECT p.*, u.nama_lengkap 
    FROM penjualan p 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$header = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$header) {
    header("Location: penjualan.php");
    exit;
}

// Detail items
$details = mysqli_query($conn, "
    SELECT d.*, pr.nama_produk, pr.gambar, pr.kategori
    FROM detail_penjualan d
    JOIN produk pr ON d.produk_id = pr.id
    WHERE d.penjualan_id = $id
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Detail Penjualan #<?= $id; ?> - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Detail Transaksi #<?= $id; ?></h1>
                <p>Informasi lengkap checkout</p>
            </div>
            <a href="penjualan.php" class="btn btn-secondary">← Kembali</a>
        </header>

        <div class="content">
            <div class="detail-box">
                <div class="row">
                    <span>Tanggal</span>
                    <strong><?= date('d M Y H:i', strtotime($header['tanggal'])); ?></strong>
                </div>
                <div class="row">
                    <span>Kasir / Pembeli</span>
                    <strong><?= htmlspecialchars($header['nama_lengkap'] ?? '-'); ?></strong>
                </div>
                <div class="row">
                    <span>Metode Pembayaran</span>
                    <strong><?= htmlspecialchars($header['metode_pembayaran'] ?? 'Tunai'); ?></strong>
                </div>
                <div class="row">
                    <span>Status</span>
                    <strong style="color:#16a34a;">● <?= ucfirst($header['status'] ?? 'lunas'); ?></strong>
                </div>
                <?php if (!empty($header['bukti_pembayaran'])): ?>
                <div class="row">
                    <span>Bukti Pembayaran</span>
                    <strong><a href="<?= htmlspecialchars($header['bukti_pembayaran']); ?>" target="_blank" style="color:#7c3aed;">📎 Lihat Bukti</a></strong>
                </div>
                <?php endif; ?>
                <div class="row">
                    <span>Keterangan</span>
                    <strong><?= htmlspecialchars($header['keterangan'] ?? '-'); ?></strong>
                </div>
                <div class="row">
                    <span>Total</span>
                    <strong class="harga" style="font-size:1.2rem;"><?= formatRupiah($header['total']); ?></strong>
                </div>
            </div>

            <h3 style="margin-bottom:14px; font-size:1.05rem;">Item yang Dibeli</h3>

            <?php if (mysqli_num_rows($details) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga Satuan</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($d = mysqli_fetch_assoc($details)): ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <?php if (!empty($d['gambar'])): ?>
                                        <img src="<?= htmlspecialchars($d['gambar']); ?>" 
                                             style="width:40px; height:40px; object-fit:cover; border-radius:6px;"
                                             onerror="this.style.display='none'">
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($d['nama_produk']); ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($d['kategori']); ?></td>
                            <td><?= formatRupiah($d['harga_satuan']); ?></td>
                            <td><?= $d['qty']; ?></td>
                            <td class="harga"><?= formatRupiah($d['subtotal']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="color:#64748b;">Tidak ada item.</p>
            <?php endif; ?>
        </div>

        <footer class="app-footer">Digital Electronic Ridho &copy; <?= date('Y'); ?></footer>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
