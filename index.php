<?php
require_once 'includes/auth.php';
requireRole(['admin', 'kasir', 'staff']);

// Statistik Produk
$total_produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM produk"))['jml'];
$total_stok   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(stok),0) AS jml FROM produk"))['jml'];
$stok_rendah  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM produk WHERE stok < 10"))['jml'];

// Pemasukan (dari penjualan)
$total_penjualan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM penjualan"))['jml'];
$pemasukan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS jml FROM penjualan"))['jml'];

// Pengeluaran
$total_pengeluaran_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengeluaran"))['jml'];
$pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS jml FROM pengeluaran"))['jml'];

// Laba
$laba = $pemasukan - $pengeluaran;

// Transaksi terbaru
$recent = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap 
    FROM penjualan p 
    LEFT JOIN users u ON p.user_id = u.id 
    ORDER BY p.id DESC LIMIT 5
");

// Pengeluaran terbaru
$recent_exp = mysqli_query($conn, "
    SELECT * FROM pengeluaran ORDER BY id DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Dashboard</h1>
                <p>Selamat datang, <?= htmlspecialchars(currentUser()['nama']); ?>! Kelola toko elektronik Anda.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="tambah_penjualan.php" class="btn btn-success">+ Checkout Baru</a>
                <a href="tambah_produk.php" class="btn btn-primary">+ Tambah Produk</a>
            </div>
        </header>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Produk</h3>
                <div class="value"><?= $total_produk; ?></div>
                <div class="sub">Item aktif</div>
            </div>
            <div class="stat-card green">
                <h3>Total Stok</h3>
                <div class="value"><?= number_format($total_stok); ?></div>
                <div class="sub">Unit tersedia</div>
            </div>
            <div class="stat-card orange">
                <h3>Stok Rendah</h3>
                <div class="value"><?= $stok_rendah; ?></div>
                <div class="sub">&lt; 10 unit</div>
            </div>
            <div class="stat-card purple">
                <h3>Transaksi</h3>
                <div class="value"><?= $total_penjualan; ?></div>
                <div class="sub">Total penjualan</div>
            </div>
            <div class="stat-card green">
                <h3>Pemasukan</h3>
                <div class="value" style="font-size:1.25rem;"><?= formatRupiah($pemasukan); ?></div>
                <div class="sub">Dari penjualan</div>
            </div>
            <div class="stat-card red">
                <h3>Pengeluaran</h3>
                <div class="value" style="font-size:1.25rem;"><?= formatRupiah($pengeluaran); ?></div>
                <div class="sub"><?= $total_pengeluaran_count; ?> catatan</div>
            </div>
            <div class="stat-card <?= $laba >= 0 ? 'green' : 'red'; ?>">
                <h3>Laba / Rugi</h3>
                <div class="value" style="font-size:1.25rem;"><?= formatRupiah($laba); ?></div>
                <div class="sub">Pemasukan − Pengeluaran</div>
            </div>
        </div>

        <div class="dashboard-panels">
            <!-- Recent Sales -->
            <div class="content">
                <h2 style="font-size:1.1rem; margin-bottom:16px;">Transaksi Terbaru</h2>
                <?php if (mysqli_num_rows($recent) > 0): ?>
                <ul class="recent-list">
                    <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                    <li>
                        <div>
                            <strong>#<?= $r['id']; ?></strong>
                            <span style="color:#64748b; font-size:0.85rem;"> — <?= htmlspecialchars($r['nama_lengkap'] ?? 'System'); ?></span>
                            <br>
                            <small style="color:#94a3b8;"><?= date('d M Y H:i', strtotime($r['tanggal'])); ?></small>
                        </div>
                        <div style="text-align:right;">
                            <span class="harga"><?= formatRupiah($r['total']); ?></span>
                            <br>
                            <a href="detail_penjualan.php?id=<?= $r['id']; ?>" class="btn btn-sm btn-outline" style="margin-top:4px;">Detail</a>
                        </div>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <a href="penjualan.php" class="btn btn-outline btn-sm" style="margin-top:12px;">Lihat Semua →</a>
                <?php else: ?>
                <div class="empty-state">
                    <div class="icon">🛒</div>
                    <h3>Belum ada transaksi</h3>
                    <a href="tambah_penjualan.php" class="btn btn-primary" style="margin-top:12px;">Buat Checkout Pertama</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Recent Expenses -->
            <div class="content">
                <h2 style="font-size:1.1rem; margin-bottom:16px;">Pengeluaran Terbaru</h2>
                <?php if (mysqli_num_rows($recent_exp) > 0): ?>
                <ul class="recent-list">
                    <?php while ($e = mysqli_fetch_assoc($recent_exp)): ?>
                    <li>
                        <div>
                            <strong><?= htmlspecialchars($e['kategori']); ?></strong>
                            <br>
                            <small style="color:#94a3b8;"><?= htmlspecialchars($e['keterangan']); ?></small>
                            <br>
                            <small style="color:#94a3b8;"><?= date('d M Y', strtotime($e['tanggal'])); ?></small>
                        </div>
                        <div style="text-align:right;">
                            <span style="color:#ef4444; font-weight:600;"><?= formatRupiah($e['jumlah']); ?></span>
                        </div>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <a href="pengeluaran.php" class="btn btn-outline btn-sm" style="margin-top:12px;">Lihat Semua →</a>
                <?php else: ?>
                <div class="empty-state">
                    <div class="icon">💸</div>
                    <h3>Belum ada pengeluaran</h3>
                    <a href="tambah_pengeluaran.php" class="btn btn-primary" style="margin-top:12px;">Catat Pengeluaran</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <footer class="app-footer">Digital Electronic Ridho &copy; <?= date('Y'); ?> — Panel Admin Toko Elektronik</footer>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
