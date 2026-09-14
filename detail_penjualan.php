<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: penjualan.php'); exit; }

$stmt = mysqli_prepare($conn, "SELECT p.*, u.nama_lengkap FROM penjualan p LEFT JOIN users u ON p.user_id=u.id WHERE p.id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$header = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$header) { header('Location: penjualan.php'); exit; }

$details = mysqli_query($conn, "SELECT d.*, pr.nama_produk, pr.gambar, pr.kategori FROM detail_penjualan d JOIN produk pr ON d.produk_id=pr.id WHERE d.penjualan_id=$id");
$items = [];
if ($details) while ($row = mysqli_fetch_assoc($details)) $items[] = $row;

$nama_toko = shopName();
$tipe_toko = getSetting('tipe_toko', 'Toko Elektronik');
$alamat_toko = getSetting('alamat', '');
$telepon_toko = getSetting('telepon', '');
$footer_struk = getSetting('footer_struk', 'Terima kasih telah berbelanja');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Detail #<?= $id; ?> - <?= htmlspecialchars($nama_toko); ?></title>
<link rel="stylesheet" href="css/style.css">
<style>
.struk-print{font-family:'Courier New',monospace;font-size:13px;max-width:320px;margin:20px auto;padding:16px;border:1px dashed #94a3b8;background:#fff;color:#111}
.struk-print .center{text-align:center}
.struk-print .line{border-top:1px dashed #999;margin:8px 0}
.struk-print .row{display:flex;justify-content:space-between;gap:8px}
.struk-print .bold{font-weight:700}
@media print{
  body *{visibility:hidden}
  .struk-print,.struk-print *{visibility:visible}
  .struk-print{position:absolute;left:0;top:0;width:100%;border:none}
  .app-nav,.bottom-nav,.page-header,.no-print,.fab-cart,.app-footer{display:none!important}
}
</style>
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<div class="container">
<header class="page-header no-print">
<div><h1>Detail #<?= $id; ?></h1><p>Transaksi & cetak struk</p></div>
<div style="display:flex;gap:8px;flex-wrap:wrap">
<button type="button" class="btn btn-primary" onclick="window.print()">🖨 Cetak Struk</button>
<a href="penjualan.php" class="btn btn-secondary">← Kembali</a>
</div>
</header>

<div class="content no-print">
<div class="detail-box">
<div class="row"><span>Tanggal</span><strong><?= date('d M Y H:i', strtotime($header['tanggal'])); ?></strong></div>
<div class="row"><span>Kasir</span><strong><?= htmlspecialchars($header['nama_lengkap'] ?? '-'); ?></strong></div>
<?php if (!empty($header['nama_pelanggan'])): ?>
<div class="row"><span>Pelanggan</span><strong><?= htmlspecialchars($header['nama_pelanggan']); ?></strong></div>
<?php endif; ?>
<div class="row"><span>Metode</span><strong><?= htmlspecialchars($header['metode_pembayaran'] ?? 'Tunai'); ?></strong></div>
<div class="row"><span>Status</span><strong style="color:#16a34a">● <?= ucfirst($header['status'] ?? 'lunas'); ?></strong></div>
<?php if (!empty($header['diskon']) && $header['diskon']>0): ?>
<div class="row"><span>Diskon</span><strong style="color:#ef4444">- <?= formatRupiah($header['diskon']); ?></strong></div>
<?php endif; ?>
<?php if (!empty($header['bayar'])): ?>
<div class="row"><span>Bayar</span><strong><?= formatRupiah($header['bayar']); ?></strong></div>
<?php endif; ?>
<?php if (!empty($header['kembalian']) && $header['kembalian']>0): ?>
<div class="row"><span>Kembalian</span><strong style="color:#059669"><?= formatRupiah($header['kembalian']); ?></strong></div>
<?php endif; ?>
<div class="row"><span>Total</span><strong class="harga" style="font-size:1.15rem"><?= formatRupiah($header['total']); ?></strong></div>
</div>
<h3 style="margin:18px 0 12px;font-size:1rem">Item</h3>
<?php if ($items): ?>
<div class="table-responsive"><table>
<thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
<tbody>
<?php foreach ($items as $d): ?>
<tr>
<td><strong><?= htmlspecialchars($d['nama_produk']); ?></strong></td>
<td><?= formatRupiah($d['harga_satuan']); ?></td>
<td><?= (int)$d['qty']; ?></td>
<td class="harga"><?= formatRupiah($d['subtotal']); ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php else: ?><p style="color:#64748b">Tidak ada item.</p><?php endif; ?>
</div>

<div class="struk-print">
<div class="center bold"><?= htmlspecialchars($nama_toko); ?></div>
<div class="center"><?= htmlspecialchars($tipe_toko); ?></div>
<?php if ($alamat_toko || $telepon_toko): ?>
<div class="center" style="font-size:11px;margin-top:4px"><?= htmlspecialchars($alamat_toko); ?><?php if($alamat_toko&&$telepon_toko): ?><br><?php endif; ?><?= htmlspecialchars($telepon_toko); ?></div>
<?php endif; ?>
<div class="line"></div>
<div class="row"><span>No</span><span>#<?= $id; ?></span></div>
<div class="row"><span>Tanggal</span><span><?= date('d/m/Y H:i', strtotime($header['tanggal'])); ?></span></div>
<div class="row"><span>Kasir</span><span><?= htmlspecialchars($header['nama_lengkap'] ?? '-'); ?></span></div>
<?php if (!empty($header['nama_pelanggan'])): ?>
<div class="row"><span>Pelanggan</span><span><?= htmlspecialchars($header['nama_pelanggan']); ?></span></div>
<?php endif; ?>
<div class="row"><span>Metode</span><span><?= htmlspecialchars($header['metode_pembayaran'] ?? 'Tunai'); ?></span></div>
<div class="line"></div>
<?php foreach ($items as $d): ?>
<div class="row"><span><?= htmlspecialchars($d['nama_produk']); ?><br><small><?= (int)$d['qty']; ?> x <?= formatRupiah($d['harga_satuan']); ?></small></span><span><?= formatRupiah($d['subtotal']); ?></span></div>
<?php endforeach; ?>
<div class="line"></div>
<?php if (!empty($header['diskon']) && $header['diskon']>0): ?>
<div class="row"><span>Diskon</span><span>- <?= formatRupiah($header['diskon']); ?></span></div>
<?php endif; ?>
<div class="row bold"><span>TOTAL</span><span><?= formatRupiah($header['total']); ?></span></div>
<?php if (!empty($header['bayar'])): ?>
<div class="row"><span>Bayar</span><span><?= formatRupiah($header['bayar']); ?></span></div>
<?php endif; ?>
<?php if (!empty($header['kembalian']) && $header['kembalian']>0): ?>
<div class="row"><span>Kembalian</span><span><?= formatRupiah($header['kembalian']); ?></span></div>
<?php endif; ?>
<div class="line"></div>
<div class="center" style="margin-top:10px"><?= htmlspecialchars($footer_struk); ?></div>
</div>
<footer class="app-footer no-print"><?= htmlspecialchars($nama_toko); ?> &copy; <?= date('Y'); ?></footer>
</div>
<script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
