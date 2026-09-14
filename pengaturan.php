<?php
require_once __DIR__ . '/includes/auth.php';
requireRole(['admin']);
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['nama_toko','tipe_toko','alamat','telepon','footer_struk'];
    foreach ($fields as $f) {
        $val = mysqli_real_escape_string($conn, trim($_POST[$f] ?? ''));
        $fk = mysqli_real_escape_string($conn, $f);
        mysqli_query($conn, "INSERT INTO pengaturan (kunci, nilai) VALUES ('$fk','$val') ON DUPLICATE KEY UPDATE nilai='$val'");
    }
    $pesan = 'Pengaturan berhasil disimpan.';
}
$s = getAllSettings();
$nama_toko = $s['nama_toko'] ?? 'Digital Electronic';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pengaturan - <?= htmlspecialchars($nama_toko); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<div class="container">
    <header class="page-header">
        <div><h1>Pengaturan Toko</h1><p>Identitas toko & teks struk</p></div>
    </header>
    <?php if ($pesan): ?><div class="alert alert-success"><?= htmlspecialchars($pesan); ?></div><?php endif; ?>
    <div class="content" style="max-width:520px;">
        <form method="POST">
            <div class="form-group"><label>Nama Toko</label>
                <input type="text" name="nama_toko" value="<?= htmlspecialchars($s['nama_toko'] ?? 'Digital Electronic'); ?>" required></div>
            <div class="form-group"><label>Tipe Usaha</label>
                <input type="text" name="tipe_toko" value="<?= htmlspecialchars($s['tipe_toko'] ?? 'Toko Elektronik'); ?>" required></div>
            <div class="form-group"><label>Alamat</label>
                <input type="text" name="alamat" value="<?= htmlspecialchars($s['alamat'] ?? ''); ?>"></div>
            <div class="form-group"><label>Telepon</label>
                <input type="text" name="telepon" value="<?= htmlspecialchars($s['telepon'] ?? ''); ?>"></div>
            <div class="form-group"><label>Footer Struk</label>
                <input type="text" name="footer_struk" value="<?= htmlspecialchars($s['footer_struk'] ?? 'Terima kasih'); ?>"></div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
