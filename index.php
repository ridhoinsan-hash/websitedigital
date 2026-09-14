<?php
require_once __DIR__ . '/includes/auth.php';
requireRole(['admin', 'kasir', 'staff']);

$total_produk = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM produk"))['jml'] ?? 0);
$total_stok   = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(stok),0) AS jml FROM produk"))['jml'] ?? 0);
$stok_rendah  = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM produk WHERE stok < 10"))['jml'] ?? 0);
$total_penjualan = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM penjualan"))['jml'] ?? 0);
$pemasukan = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS jml FROM penjualan"))['jml'] ?? 0);
$total_pengeluaran_count = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengeluaran"))['jml'] ?? 0);
$pengeluaran = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS jml FROM pengeluaran"))['jml'] ?? 0);
$laba = $pemasukan - $pengeluaran;

$labels = []; $pemasukan_harian = []; $pengeluaran_harian = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($tgl));
    $q1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS jml FROM penjualan WHERE DATE(tanggal) = '$tgl'"));
    $pemasukan_harian[] = (float)($q1['jml'] ?? 0);
    $q2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS jml FROM pengeluaran WHERE DATE(tanggal) = '$tgl'"));
    $pengeluaran_harian[] = (float)($q2['jml'] ?? 0);
}

$recent = mysqli_query($conn, "SELECT p.*, u.nama_lengkap FROM penjualan p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.id DESC LIMIT 5");
$recent_exp = mysqli_query($conn, "SELECT * FROM pengeluaran ORDER BY id DESC LIMIT 5");
$top = mysqli_query($conn, "SELECT pr.nama_produk, SUM(d.qty) AS terjual FROM detail_penjualan d JOIN produk pr ON d.produk_id = pr.id GROUP BY d.produk_id ORDER BY terjual DESC LIMIT 5");

$nama_toko = shopName();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard - <?= htmlspecialchars($nama_toko); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<div class="container">
    <header class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Halo, <?= htmlspecialchars(currentUser()['nama']); ?> — ringkasan toko hari ini.</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="tambah_penjualan.php" class="btn btn-success">+ Kasir</a>
            <?php if (hasRole('admin')): ?>
            <a href="portofolio.php" class="btn btn-primary">Portofolio</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="stats-grid">
        <div class="stat-card"><h3>Produk</h3><div class="value"><?= $total_produk; ?></div><div class="sub">Item aktif</div></div>
        <div class="stat-card green"><h3>Stok</h3><div class="value"><?= number_format($total_stok); ?></div><div class="sub">Unit</div></div>
        <div class="stat-card orange"><h3>Stok Rendah</h3><div class="value"><?= $stok_rendah; ?></div><div class="sub">&lt; 10 unit</div></div>
        <div class="stat-card purple"><h3>Transaksi</h3><div class="value"><?= $total_penjualan; ?></div><div class="sub">Total</div></div>
        <div class="stat-card green"><h3>Pemasukan</h3><div class="value" style="font-size:1.15rem;"><?= formatRupiah($pemasukan); ?></div><div class="sub">Penjualan</div></div>
        <div class="stat-card red"><h3>Pengeluaran</h3><div class="value" style="font-size:1.15rem;"><?= formatRupiah($pengeluaran); ?></div><div class="sub"><?= $total_pengeluaran_count; ?> catatan</div></div>
        <div class="stat-card <?= $laba >= 0 ? 'green' : 'red'; ?>"><h3>Laba / Rugi</h3><div class="value" style="font-size:1.15rem;"><?= formatRupiah($laba); ?></div><div class="sub">Bersih</div></div>
    </div>

    <div class="chart-grid">
        <div class="chart-card"><h3>📈 Pemasukan 7 Hari</h3><div class="chart-wrap"><canvas id="cIn"></canvas></div></div>
        <div class="chart-card"><h3>📉 Pengeluaran 7 Hari</h3><div class="chart-wrap"><canvas id="cOut"></canvas></div></div>
    </div>
    <div class="chart-card" style="margin-bottom:22px;"><h3>📊 Pemasukan vs Pengeluaran</h3><div class="chart-wrap" style="height:260px;"><canvas id="cCmp"></canvas></div></div>

    <div class="dashboard-panels">
        <div class="content">
            <h2 style="font-size:1.05rem;margin-bottom:12px;">Transaksi Terbaru</h2>
            <?php if ($recent && mysqli_num_rows($recent) > 0): ?>
            <ul class="recent-list">
                <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                <li>
                    <div>
                        <strong>#<?= (int)$r['id']; ?></strong>
                        <span style="color:#64748b;font-size:.85rem;"> — <?= htmlspecialchars($r['nama_lengkap'] ?? '-'); ?></span><br>
                        <small style="color:#94a3b8;"><?= date('d M Y H:i', strtotime($r['tanggal'])); ?></small>
                    </div>
                    <div style="text-align:right;">
                        <span class="harga"><?= formatRupiah($r['total']); ?></span><br>
                        <a href="detail_penjualan.php?id=<?= (int)$r['id']; ?>" class="btn btn-sm btn-outline" style="margin-top:4px;">Detail</a>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
            <a href="penjualan.php" class="btn btn-outline btn-sm" style="margin-top:10px;">Lihat semua →</a>
            <?php else: ?>
            <div class="empty-state"><div class="icon">🛒</div><h3>Belum ada transaksi</h3></div>
            <?php endif; ?>
        </div>
        <div class="content">
            <h2 style="font-size:1.05rem;margin-bottom:12px;">Produk Terlaris</h2>
            <?php if ($top && mysqli_num_rows($top) > 0): ?>
            <ul class="recent-list">
                <?php $rank=1; while ($t = mysqli_fetch_assoc($top)): ?>
                <li>
                    <div><strong>#<?= $rank++; ?> <?= htmlspecialchars($t['nama_produk']); ?></strong></div>
                    <div style="text-align:right;font-weight:700;color:#4f46e5;"><?= (int)$t['terjual']; ?> terjual</div>
                </li>
                <?php endwhile; ?>
            </ul>
            <?php else: ?>
            <p style="color:#94a3b8;">Belum ada data penjualan detail.</p>
            <?php endif; ?>
            <h2 style="font-size:1.05rem;margin:18px 0 12px;">Pengeluaran Terbaru</h2>
            <?php if ($recent_exp && mysqli_num_rows($recent_exp) > 0): ?>
            <ul class="recent-list">
                <?php while ($e = mysqli_fetch_assoc($recent_exp)): ?>
                <li>
                    <div>
                        <strong><?= htmlspecialchars($e['kategori']); ?></strong><br>
                        <small style="color:#94a3b8;"><?= date('d M Y', strtotime($e['tanggal'])); ?></small>
                    </div>
                    <span style="color:#ef4444;font-weight:600;"><?= formatRupiah($e['jumlah']); ?></span>
                </li>
                <?php endwhile; ?>
            </ul>
            <?php else: ?>
            <p style="color:#94a3b8;">Belum ada pengeluaran.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer class="app-footer"><?= htmlspecialchars($nama_toko); ?> &copy; <?= date('Y'); ?></footer>
</div>
<script src="js/script.js"></script>
<script>
const labels = <?= json_encode($labels); ?>;
const dataIn = <?= json_encode($pemasukan_harian); ?>;
const dataOut = <?= json_encode($pengeluaran_harian); ?>;
const fmt = v => 'Rp ' + Number(v).toLocaleString('id-ID');
const barOpt = { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{callback:v=>fmt(v)}}} };
new Chart(document.getElementById('cIn'), { type:'bar', data:{ labels, datasets:[{ data:dataIn, backgroundColor:'rgba(16,185,129,.7)', borderRadius:6 }] }, options:barOpt });
new Chart(document.getElementById('cOut'), { type:'bar', data:{ labels, datasets:[{ data:dataOut, backgroundColor:'rgba(239,68,68,.7)', borderRadius:6 }] }, options:barOpt });
new Chart(document.getElementById('cCmp'), {
  type:'line',
  data:{ labels, datasets:[
    { label:'Pemasukan', data:dataIn, borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.12)', fill:true, tension:.3 },
    { label:'Pengeluaran', data:dataOut, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.1)', fill:true, tension:.3 }
  ]},
  options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true,ticks:{callback:v=>fmt(v)}}} }
});
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
