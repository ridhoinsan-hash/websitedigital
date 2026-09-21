<?php
require_once 'includes/auth.php';
requireLogin();

$user = currentUser();
if ($user['role'] !== 'pembeli') {
    header('Location: index.php');
    exit;
}

$uid = (int)$user['id'];

$jml_order = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS j FROM penjualan WHERE user_id = $uid"))['j'] ?? 0);
$total_belanja = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS j FROM penjualan WHERE user_id = $uid AND status = 'lunas'"))['j'] ?? 0);
$pending = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS j FROM penjualan WHERE user_id = $uid AND status = 'pending'"))['j'] ?? 0);

$riwayat = mysqli_query($conn, "SELECT * FROM penjualan WHERE user_id = $uid ORDER BY id DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard Saya - Digital Electronic</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="container">
    <header class="page-header">
        <div>
            <h1>Halo, <?= htmlspecialchars($user['nama']); ?> 👋</h1>
            <p>Ringkasan akun & riwayat belanja Anda</p>
        </div>
        <a href="produk.php" class="btn btn-success">🛒 Belanja</a>
    </header>

    <div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px;">
        <div class="stat-card" style="background:#fff;border-radius:12px;padding:20px;">
            <div style="font-size:0.8rem;color:#64748b;">TOTAL ORDER</div>
            <div style="font-size:1.8rem;font-weight:700;"><?= $jml_order; ?></div>
        </div>
        <div class="stat-card" style="background:#fff;border-radius:12px;padding:20px;">
            <div style="font-size:0.8rem;color:#64748b;">TOTAL BELANJA</div>
            <div style="font-size:1.4rem;font-weight:700;"><?= formatRupiah($total_belanja); ?></div>
        </div>
        <div class="stat-card" style="background:#fff;border-radius:12px;padding:20px;">
            <div style="font-size:0.8rem;color:#64748b;">PENDING</div>
            <div style="font-size:1.8rem;font-weight:700;"><?= $pending; ?></div>
        </div>
    </div>

    <div class="content" style="background:#fff;border-radius:12px;padding:20px;">
        <h3 style="margin-top:0;">Riwayat Pesanan Terakhir</h3>
        <?php if ($riwayat && mysqli_num_rows($riwayat) > 0): ?>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.9rem;">
                <thead>
                    <tr style="text-align:left;border-bottom:2px solid #e2e8f0;">
                        <th style="padding:10px;">ID</th>
                        <th style="padding:10px;">Tanggal</th>
                        <th style="padding:10px;">Total</th>
                        <th style="padding:10px;">Metode</th>
                        <th style="padding:10px;">Status</th>
                        <th style="padding:10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px;">#<?= (int)$r['id']; ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['tanggal']))); ?></td>
                        <td style="padding:10px;"><?= formatRupiah($r['total']); ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($r['metode_pembayaran'] ?? '-'); ?></td>
                        <td style="padding:10px;"><span class="badge"><?= htmlspecialchars($r['status']); ?></span></td>
                        <td style="padding:10px;"><a href="detail_penjualan.php?id=<?= (int)$r['id']; ?>" class="btn btn-sm btn-secondary">Detail</a></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:#64748b;">Belum ada pesanan. <a href="produk.php">Mulai belanja</a></p>
        <?php endif; ?>
    </div>

    <div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap;">
        <a href="produk.php" class="btn btn-primary">Katalog Produk</a>
        <a href="tambah_penjualan.php" class="btn btn-success">Checkout</a>
        <a href="profil.php" class="btn btn-secondary">Profil & Ganti Password</a>
    </div>

    <footer class="app-footer" style="margin-top:32px;">Digital Electronic &copy; <?= date('Y'); ?></footer>
</div>
<script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
