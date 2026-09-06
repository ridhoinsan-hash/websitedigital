<?php
require_once 'includes/auth.php';
requireLogin(); // semua role bisa akses

$userRole = currentUser()['role'];

$pesan = '';
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'sukses_tambah') $pesan = '<div class="alert alert-success">Produk berhasil ditambahkan!</div>';
    if ($_GET['pesan'] === 'sukses_edit')   $pesan = '<div class="alert alert-success">Produk berhasil diperbarui!</div>';
    if ($_GET['pesan'] === 'sukses_hapus')  $pesan = '<div class="alert alert-success">Produk berhasil dihapus!</div>';
    if ($_GET['pesan'] === 'gagal_hapus')   $pesan = '<div class="alert alert-danger">Produk tidak bisa dihapus karena sudah pernah terjual.</div>';
    if ($_GET['pesan'] === 'sukses_beli')   $pesan = '<div class="alert alert-success">Pembelian berhasil! Terima kasih sudah berbelanja di Digital Electronic Ridho.</div>';
}

$result = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= $userRole === 'pembeli' ? 'Katalog' : 'Produk'; ?> - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1><?= $userRole === 'pembeli' ? 'Katalog Produk' : 'Katalog Produk'; ?></h1>
                <p><?= $userRole === 'pembeli' ? 'Pilih produk elektronik favorit Anda' : 'Kelola inventaris toko elektronik'; ?></p>
            </div>
            <?php if ($userRole === 'admin'): ?>
                <a href="tambah_produk.php" class="btn btn-success">+ Tambah Produk</a>
            <?php elseif ($userRole === 'pembeli'): ?>
                <a href="tambah_penjualan.php" class="btn btn-success">🛒 Checkout</a>
            <?php endif; ?>
        </header>

        <?= $pesan; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="product-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $stokClass = ($row['stok'] < 10) ? 'stok-low' : 'stok-ok';
            ?>
            <div class="product-card">
                <div class="img-wrap">
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_produk']); ?>" 
                             onerror="this.parentElement.innerHTML='<div class=\'img-placeholder\'>📱</div>'">
                    <?php else: ?>
                        <div class="img-placeholder">📱</div>
                    <?php endif; ?>
                </div>
                <div class="body">
                    <div class="kategori"><?= htmlspecialchars($row['kategori']); ?></div>
                    <h3><?= htmlspecialchars($row['nama_produk']); ?></h3>
                    <div class="harga"><?= formatRupiah($row['harga']); ?></div>
                    <div class="meta">
                        <span class="stok-badge <?= $stokClass; ?>">Stok: <?= $row['stok']; ?></span>
                    </div>
                    <div class="actions">
                        <?php if ($userRole === 'admin'): ?>
                            <a href="edit_produk.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="hapus_produk.php?id=<?= $row['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return konfirmasiHapus('<?= htmlspecialchars(addslashes($row['nama_produk'])); ?>')">Hapus</a>
                        <?php elseif ($userRole === 'pembeli' || $userRole === 'kasir'): ?>
                            <?php if ($row['stok'] > 0): ?>
                                <a href="tambah_penjualan.php?produk_id=<?= $row['id']; ?>" class="btn btn-success btn-sm">Beli / Checkout</a>
                            <?php else: ?>
                                <span class="btn btn-secondary btn-sm" style="opacity:0.6;cursor:not-allowed;">Stok Habis</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="content">
            <div class="empty-state">
                <div class="icon">📦</div>
                <h3>Belum ada produk</h3>
                <p><?= $userRole === 'admin' ? 'Tambahkan produk elektronik pertama Anda' : 'Produk sedang kosong'; ?></p>
                <?php if ($userRole === 'admin'): ?>
                <a href="tambah_produk.php" class="btn btn-primary" style="margin-top:12px;">+ Tambah Produk</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <footer class="app-footer">Digital Electronic Ridho &copy; <?= date('Y'); ?></footer>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
