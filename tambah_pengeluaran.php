<?php
require_once 'includes/auth.php';
requireRole('admin');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal    = $_POST['tanggal'] ?? date('Y-m-d');
    $kategori   = trim($_POST['kategori'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $jumlah     = floatval($_POST['jumlah'] ?? 0);

    if ($kategori === '' || $keterangan === '' || $jumlah <= 0) {
        $error = 'Semua field wajib diisi dengan benar!';
    } else {
        $user_id = currentUser()['id'];
        $stmt = mysqli_prepare($conn, "INSERT INTO pengeluaran (tanggal, kategori, keterangan, jumlah, user_id) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssdi", $tanggal, $kategori, $keterangan, $jumlah, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: pengeluaran.php?pesan=sukses_tambah");
            exit;
        }
        $error = 'Gagal menyimpan: ' . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Tambah Pengeluaran - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Catat Pengeluaran</h1>
                <p>Tambah catatan biaya operasional</p>
            </div>
            <a href="pengeluaran.php" class="btn btn-secondary">← Kembali</a>
        </header>

        <div class="content">
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>

            <div class="form-card">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal">Tanggal *</label>
                            <input type="date" id="tanggal" name="tanggal" 
                                   value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="kategori">Kategori *</label>
                            <select id="kategori" name="kategori" required>
                                <option value="">-- Pilih --</option>
                                <?php
                                $kats = ['Operasional','Gaji','Persediaan','Marketing','Sewa','Utilitas','Lainnya'];
                                $selected = $_POST['kategori'] ?? '';
                                foreach ($kats as $k) {
                                    $sel = ($selected === $k) ? 'selected' : '';
                                    echo "<option value=\"$k\" $sel>$k</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan *</label>
                        <input type="text" id="keterangan" name="keterangan"
                               value="<?= htmlspecialchars($_POST['keterangan'] ?? ''); ?>"
                               placeholder="Contoh: Biaya listrik bulan Agustus" required>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah (Rp) *</label>
                        <input type="number" id="jumlah" name="jumlah" min="1" step="1000"
                               value="<?= htmlspecialchars($_POST['jumlah'] ?? ''); ?>"
                               placeholder="500000" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger">Simpan Pengeluaran</button>
                        <a href="pengeluaran.php" class="btn btn-secondary">Batal</a>
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
