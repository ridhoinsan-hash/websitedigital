<?php
require_once __DIR__ . '/includes/auth.php';
requireRole(['admin', 'kasir', 'staff']);

$nama_toko = shopName();
$tipe = getSetting('tipe_toko', 'Toko Elektronik');
$alamat = getSetting('alamat', '-');
$telepon = getSetting('telepon', '-');

$total_produk = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS j FROM produk"))['j'] ?? 0);
$total_user = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS j FROM users"))['j'] ?? 0);
$total_trx = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS j FROM penjualan"))['j'] ?? 0);
$omzet = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS j FROM penjualan"))['j'] ?? 0);
$pengeluaran = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS j FROM pengeluaran"))['j'] ?? 0);
$laba = $omzet - $pengeluaran;
$bulan_ini = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS j FROM penjualan WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())"))['j'] ?? 0);
$hari_ini = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS j FROM penjualan WHERE DATE(tanggal)=CURDATE()"))['j'] ?? 0);
$trx_hari = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS j FROM penjualan WHERE DATE(tanggal)=CURDATE()"))['j'] ?? 0);

$fitur = [
    ['ico'=>'🛒', 't'=>'Kasir & Keranjang', 'd'=>'Multi-item, diskon, kembalian, multi metode bayar'],
    ['ico'=>'📦', 't'=>'Manajemen Produk', 'd'=>'CRUD produk, stok, kategori, gambar'],
    ['ico'=>'🧾', 't'=>'Cetak Struk', 'd'=>'Struk thermal-ready dengan identitas toko'],
    ['ico'=>'📊', 't'=>'Dashboard Analitik', 'd'=>'Grafik pemasukan & pengeluaran 7 hari'],
    ['ico'=>'👥', 't'=>'Multi Role', 'd'=>'Admin, Kasir, Staff, Pembeli'],
    ['ico'=>'📱', 't'=>'Responsif', 'd'=>'Optimal di PC, laptop, tablet, dan HP'],
    ['ico'=>'⚙️', 't'=>'Pengaturan Toko', 'd'=>'Nama, alamat, footer struk'],
    ['ico'=>'💸', 't'=>'Pengeluaran', 'd'=>'Catat biaya operasional toko'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Portofolio - <?= htmlspecialchars($nama_toko); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<div class="container">
    <div class="porto-hero">
        <h1>⚡ <?= htmlspecialchars($nama_toko); ?></h1>
        <p><?= htmlspecialchars($tipe); ?> · Sistem kasir & manajemen toko elektronik</p>
        <p style="margin-top:10px;font-size:.85rem;opacity:.85;">
            <?= htmlspecialchars($alamat); ?> · <?= htmlspecialchars($telepon); ?>
        </p>
    </div>

    <h2 style="font-size:1.1rem;margin-bottom:12px;">Ringkasan Performa</h2>
    <div class="porto-grid">
        <div class="porto-card"><div class="ico">📅</div><h4>Omzet Hari Ini</h4><div class="val"><?= formatRupiah($hari_ini); ?></div><small style="color:#64748b;"><?= $trx_hari; ?> transaksi</small></div>
        <div class="porto-card"><div class="ico">📆</div><h4>Omzet Bulan Ini</h4><div class="val"><?= formatRupiah($bulan_ini); ?></div></div>
        <div class="porto-card"><div class="ico">💰</div><h4>Total Omzet</h4><div class="val"><?= formatRupiah($omzet); ?></div></div>
        <div class="porto-card"><div class="ico">📈</div><h4>Laba Bersih</h4><div class="val" style="color:<?= $laba>=0?'#059669':'#dc2626'; ?>"><?= formatRupiah($laba); ?></div></div>
        <div class="porto-card"><div class="ico">📦</div><h4>Produk</h4><div class="val"><?= $total_produk; ?></div></div>
        <div class="porto-card"><div class="ico">🧾</div><h4>Transaksi</h4><div class="val"><?= $total_trx; ?></div></div>
        <div class="porto-card"><div class="ico">👤</div><h4>Pengguna</h4><div class="val"><?= $total_user; ?></div></div>
        <div class="porto-card"><div class="ico">💸</div><h4>Pengeluaran</h4><div class="val"><?= formatRupiah($pengeluaran); ?></div></div>
    </div>

    <h2 style="font-size:1.1rem;margin:8px 0 12px;">Fitur Aplikasi</h2>
    <div class="porto-grid">
        <?php foreach ($fitur as $f): ?>
        <div class="porto-card">
            <div class="ico"><?= $f['ico']; ?></div>
            <h4 style="text-transform:none;letter-spacing:0;color:#1e1b4b;font-size:.95rem;"><?= $f['t']; ?></h4>
            <p style="margin:4px 0 0;font-size:.82rem;color:#64748b;"><?= $f['d']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="content" style="margin-top:8px;">
        <h2 style="font-size:1.05rem;margin-bottom:10px;">Tentang Proyek</h2>
        <p style="color:#475569;line-height:1.6;font-size:.95rem;">
            <strong><?= htmlspecialchars($nama_toko); ?></strong> adalah aplikasi Point of Sale (POS) berbasis web
            untuk toko elektronik. Dibangun dengan PHP &amp; MySQL, mendukung multi-perangkat
            (PC, laptop, tablet, smartphone), multi-role, keranjang belanja, cetak struk, dan laporan analitik.
        </p>
        <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
            <a href="index.php" class="btn btn-primary">Dashboard</a>
            <a href="tambah_penjualan.php" class="btn btn-success">Buka Kasir</a>
            <?php if (hasRole('admin')): ?>
            <a href="pengaturan.php" class="btn btn-secondary">Pengaturan Toko</a>
            <?php endif; ?>
        </div>
    </div>
    <footer class="app-footer"><?= htmlspecialchars($nama_toko); ?> &copy; <?= date('Y'); ?></footer>
</div>
<script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
