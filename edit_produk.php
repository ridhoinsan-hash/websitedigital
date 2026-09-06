<?php
require_once 'includes/auth.php';
requireRole('admin');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: produk.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$produk) {
    header("Location: produk.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_produk'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $harga     = floatval($_POST['harga'] ?? 0);
    $stok      = intval($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $gambar    = trim($_POST['gambar'] ?? '');

    if ($nama === '' || $kategori === '' || $harga <= 0) {
        $error = 'Nama, kategori, dan harga wajib diisi dengan benar!';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE produk SET nama_produk=?, kategori=?, harga=?, stok=?, deskripsi=?, gambar=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssdissi", $nama, $kategori, $harga, $stok, $deskripsi, $gambar, $id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: produk.php?pesan=sukses_edit");
            exit;
        }
        $error = 'Gagal menyimpan: ' . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
} else {
    // prefill
    $_POST = $produk;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Edit Produk - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Edit Produk</h1>
                <p>Perbarui data produk #<?= $id; ?></p>
            </div>
            <a href="produk.php" class="btn btn-secondary">← Kembali</a>
        </header>

        <div class="content">
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>

            <div class="form-card">
                <form method="POST" id="formProduk">
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk *</label>
                        <input type="text" id="nama_produk" name="nama_produk" 
                               value="<?= htmlspecialchars($_POST['nama_produk'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="kategori">Kategori *</label>
                            <select id="kategori" name="kategori" required>
                                <?php
                                $kats = ['Laptop','Smartphone','Tablet','Monitor','Audio','Aksesoris','Storage','Lainnya'];
                                $selected = $_POST['kategori'] ?? '';
                                foreach ($kats as $k) {
                                    $sel = ($selected === $k) ? 'selected' : '';
                                    echo "<option value=\"$k\" $sel>$k</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga (Rp) *</label>
                            <input type="number" id="harga" name="harga" min="1" step="1000"
                                   value="<?= htmlspecialchars($_POST['harga'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="stok">Stok *</label>
                            <input type="number" id="stok" name="stok" min="0"
                                   value="<?= htmlspecialchars($_POST['stok'] ?? '0'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="gambar">URL Gambar</label>
                            <input type="url" id="gambar" name="gambar"
                                   value="<?= htmlspecialchars($_POST['gambar'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Preview Gambar</label>
                        <?php if (!empty($_POST['gambar'])): ?>
                            <img id="preview_gambar" class="img-preview" src="<?= htmlspecialchars($_POST['gambar']); ?>" alt="Preview">
                        <?php else: ?>
                            <img id="preview_gambar" class="img-preview" style="display:none;" alt="Preview">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi"><?= htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        <a href="produk.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <footer class="app-footer">Digital Electronic Ridho &copy; <?= date('Y'); ?></footer>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
