<?php
require_once 'includes/auth.php';
requireLogin();

$error = '';
$preselect_id = intval($_GET['produk_id'] ?? 0);
$produk_list = mysqli_query($conn, "SELECT id, nama_produk, harga, stok, gambar, kategori FROM produk WHERE stok > 0 ORDER BY nama_produk");

// Pastikan folder uploads ada
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produk_id   = intval($_POST['produk_id'] ?? 0);
    $qty         = intval($_POST['qty'] ?? 0);
    $keterangan  = trim($_POST['keterangan'] ?? '');
    $metode      = trim($_POST['metode_pembayaran'] ?? 'Tunai');
    $bukti_path  = null;

    $allowed_metode = ['Tunai', 'Transfer Bank', 'QRIS', 'E-Wallet', 'Kartu Debit/Kredit'];
    if (!in_array($metode, $allowed_metode)) {
        $metode = 'Tunai';
    }

    if ($produk_id <= 0 || $qty <= 0) {
        $error = 'Pilih produk dan isi jumlah dengan benar!';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $produk_id);
        mysqli_stmt_execute($stmt);
        $produk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$produk) {
            $error = 'Produk tidak ditemukan!';
        } elseif ($produk['stok'] < $qty) {
            $error = 'Stok tidak mencukupi! Stok tersedia: ' . $produk['stok'];
        } else {
            // Upload bukti pembayaran (wajib jika bukan Tunai)
            if ($metode !== 'Tunai') {
                if (empty($_FILES['bukti_pembayaran']['name'])) {
                    $error = 'Upload bukti pembayaran wajib untuk metode ' . $metode . '!';
                } else {
                    $file = $_FILES['bukti_pembayaran'];
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $max_size = 3 * 1024 * 1024; // 3MB

                    if (!in_array($ext, $allowed_ext)) {
                        $error = 'Format file tidak didukung. Gunakan JPG, PNG, WEBP, atau PDF.';
                    } elseif ($file['size'] > $max_size) {
                        $error = 'Ukuran file maksimal 3MB.';
                    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
                        $error = 'Gagal upload file. Coba lagi.';
                    } else {
                        $new_name = 'bukti_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $dest = $upload_dir . $new_name;
                        if (move_uploaded_file($file['tmp_name'], $dest)) {
                            $bukti_path = 'uploads/' . $new_name;
                        } else {
                            $error = 'Gagal menyimpan file bukti pembayaran.';
                        }
                    }
                }
            }

            if (empty($error)) {
                $harga_satuan = $produk['harga'];
                $subtotal = $harga_satuan * $qty;
                $user_id = currentUser()['id'];
                $status = 'lunas';

                mysqli_begin_transaction($conn);
                try {
                    $stmt = mysqli_prepare($conn, "INSERT INTO penjualan (total, keterangan, metode_pembayaran, bukti_pembayaran, status, user_id) VALUES (?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "dssssi", $subtotal, $keterangan, $metode, $bukti_path, $status, $user_id);
                    mysqli_stmt_execute($stmt);
                    $penjualan_id = mysqli_insert_id($conn);
                    mysqli_stmt_close($stmt);

                    $stmt = mysqli_prepare($conn, "INSERT INTO detail_penjualan (penjualan_id, produk_id, qty, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "iiidd", $penjualan_id, $produk_id, $qty, $harga_satuan, $subtotal);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    $stmt = mysqli_prepare($conn, "UPDATE produk SET stok = stok - ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "ii", $qty, $produk_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    mysqli_commit($conn);

                    // Redirect ke halaman sukses
                    header("Location: sukses_pembayaran.php?id=" . $penjualan_id);
                    exit;
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $error = 'Gagal menyimpan transaksi: ' . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Checkout - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .checkout-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.9fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .checkout-grid { grid-template-columns: 1fr; }
        }
        .checkout-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(30,27,75,0.08);
            padding: 28px;
        }
        .checkout-card h2 {
            font-size: 1.15rem;
            margin: 0 0 20px;
            color: #1e1b4b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .step-num {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, #3b82f6, #7c3aed);
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .product-preview {
            display: none;
            align-items: center;
            gap: 14px;
            padding: 14px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 18px;
            border: 1px solid #e2e8f0;
        }
        .product-preview.visible { display: flex; }
        .product-preview img {
            width: 72px; height: 72px;
            object-fit: cover;
            border-radius: 10px;
            background: #e2e8f0;
        }
        .product-preview .info strong {
            display: block;
            color: #1e1b4b;
            font-size: 0.95rem;
        }
        .product-preview .info span {
            color: #64748b;
            font-size: 0.82rem;
        }
        .product-preview .harga-prev {
            margin-left: auto;
            font-weight: 700;
            color: #7c3aed;
            font-size: 1rem;
        }
        .metode-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 18px;
        }
        .metode-option {
            position: relative;
        }
        .metode-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .metode-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 14px 10px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
            transition: all 0.2s;
            text-align: center;
        }
        .metode-option label span.icon { font-size: 1.5rem; }
        .metode-option input:checked + label {
            border-color: #7c3aed;
            background: #f5f3ff;
            color: #5b21b6;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.15);
        }
        .upload-zone {
            border: 2px dashed #c4b5fd;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            background: #faf5ff;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 8px;
        }
        .upload-zone:hover, .upload-zone.dragover {
            border-color: #7c3aed;
            background: #f3e8ff;
        }
        .upload-zone .icon-up { font-size: 2rem; margin-bottom: 6px; }
        .upload-zone p { margin: 0; font-size: 0.88rem; color: #6b7280; }
        .upload-zone small { color: #9ca3af; font-size: 0.78rem; }
        .upload-preview {
            display: none;
            margin-top: 10px;
            padding: 10px;
            background: #f0fdf4;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #166534;
            align-items: center;
            gap: 8px;
        }
        .upload-preview.visible { display: flex; }
        .summary-box {
            background: linear-gradient(145deg, #1e1b4b 0%, #312e81 60%, #4c1d95 100%);
            color: #fff;
            border-radius: 16px;
            padding: 24px;
            position: sticky;
            top: 20px;
        }
        .summary-box h3 {
            margin: 0 0 18px;
            font-size: 1.05rem;
            opacity: 0.9;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.92rem;
        }
        .summary-row.total {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 14px;
            margin-top: 8px;
            font-size: 1.25rem;
            font-weight: 800;
        }
        .summary-box .btn-checkout {
            width: 100%;
            margin-top: 20px;
            padding: 14px;
            background: #fff;
            color: #4c1d95;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .summary-box .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .bukti-section { display: none; }
        .bukti-section.visible { display: block; }
        #subtotal_display { font-size: 1.1rem; font-weight: 700; color: #7c3aed; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <header class="page-header">
            <div>
                <h1>Checkout</h1>
                <p>Pilih produk, metode pembayaran, dan unggah bukti</p>
            </div>
            <a href="<?= hasRole('pembeli') ? 'produk.php' : 'penjualan.php'; ?>" class="btn btn-secondary">← Kembali</a>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="formCheckout">
            <div class="checkout-grid">
                <!-- LEFT: Form -->
                <div>
                    <div class="checkout-card" style="margin-bottom:20px;">
                        <h2><span class="step-num">1</span> Pilih Produk</h2>

                        <div class="product-preview" id="productPreview">
                            <img id="prevImg" src="" alt="">
                            <div class="info">
                                <strong id="prevNama">-</strong>
                                <span id="prevKat">-</span>
                            </div>
                            <div class="harga-prev" id="prevHarga">-</div>
                        </div>

                        <div class="form-group">
                            <label for="produk_id">Produk *</label>
                            <select id="produk_id" name="produk_id" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php 
                                mysqli_data_seek($produk_list, 0);
                                while ($p = mysqli_fetch_assoc($produk_list)): 
                                ?>
                                <option value="<?= $p['id']; ?>"
                                        data-harga="<?= $p['harga']; ?>"
                                        data-stok="<?= $p['stok']; ?>"
                                        data-nama="<?= htmlspecialchars($p['nama_produk']); ?>"
                                        data-kat="<?= htmlspecialchars($p['kategori']); ?>"
                                        data-gambar="<?= htmlspecialchars($p['gambar'] ?? ''); ?>"
                                        <?= ((isset($_POST['produk_id']) && $_POST['produk_id'] == $p['id']) || (!isset($_POST['produk_id']) && $preselect_id == $p['id'])) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($p['nama_produk']); ?>
                                    — <?= formatRupiah($p['harga']); ?>
                                    (Stok: <?= $p['stok']; ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="qty">Jumlah *</label>
                            <input type="number" id="qty" name="qty" min="1" 
                                   value="<?= htmlspecialchars($_POST['qty'] ?? '1'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Catatan (opsional)</label>
                            <input type="text" id="keterangan" name="keterangan"
                                   value="<?= htmlspecialchars($_POST['keterangan'] ?? ''); ?>"
                                   placeholder="Contoh: Kirim ke alamat ...">
                        </div>
                    </div>

                    <div class="checkout-card">
                        <h2><span class="step-num">2</span> Metode Pembayaran</h2>

                        <div class="metode-grid">
                            <?php
                            $metodes = [
                                'Tunai' => '💵',
                                'Transfer Bank' => '🏦',
                                'QRIS' => '📱',
                                'E-Wallet' => '💳',
                                'Kartu Debit/Kredit' => '💳',
                            ];
                            $curMetode = $_POST['metode_pembayaran'] ?? 'Tunai';
                            foreach ($metodes as $m => $icon):
                            ?>
                            <div class="metode-option">
                                <input type="radio" name="metode_pembayaran" id="metode_<?= md5($m); ?>" 
                                       value="<?= $m; ?>" <?= $curMetode === $m ? 'checked' : ''; ?>>
                                <label for="metode_<?= md5($m); ?>">
                                    <span class="icon"><?= $icon; ?></span>
                                    <?= $m; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="bukti-section" id="buktiSection">
                            <label style="font-weight:600; margin-bottom:8px; display:block;">Bukti Pembayaran *</label>
                            <div class="upload-zone" id="uploadZone" onclick="document.getElementById('bukti_pembayaran').click()">
                                <div class="icon-up">📤</div>
                                <p>Klik atau seret file ke sini</p>
                                <small>JPG, PNG, WEBP, PDF • Maks. 3MB</small>
                            </div>
                            <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" 
                                   accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none;">
                            <div class="upload-preview" id="uploadPreview">
                                <span>✅</span>
                                <span id="fileName">File dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Summary -->
                <div class="summary-box">
                    <h3>Ringkasan Pesanan</h3>
                    <div class="summary-row">
                        <span>Produk</span>
                        <span id="sumNama">-</span>
                    </div>
                    <div class="summary-row">
                        <span>Jumlah</span>
                        <span id="sumQty">1</span>
                    </div>
                    <div class="summary-row">
                        <span>Harga satuan</span>
                        <span id="sumHarga">Rp 0</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="sumTotal">Rp 0</span>
                    </div>
                    <button type="submit" class="btn-checkout">🔒 Proses Pembayaran</button>
                    <p style="text-align:center; margin:12px 0 0; font-size:0.75rem; opacity:0.7;">
                        Data aman & terenkripsi
                    </p>
                </div>
            </div>
        </form>
    </div>

    <script>
    (function() {
        const sel = document.getElementById('produk_id');
        const qty = document.getElementById('qty');
        const preview = document.getElementById('productPreview');
        const metodeRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
        const buktiSection = document.getElementById('buktiSection');
        const fileInput = document.getElementById('bukti_pembayaran');
        const uploadPreview = document.getElementById('uploadPreview');
        const fileName = document.getElementById('fileName');
        const uploadZone = document.getElementById('uploadZone');

        function formatRp(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        function update() {
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.value) {
                preview.classList.remove('visible');
                document.getElementById('sumNama').textContent = '-';
                document.getElementById('sumHarga').textContent = 'Rp 0';
                document.getElementById('sumTotal').textContent = 'Rp 0';
                document.getElementById('sumQty').textContent = qty.value || 1;
                return;
            }
            const harga = parseFloat(opt.dataset.harga) || 0;
            const stok = parseInt(opt.dataset.stok) || 0;
            const q = parseInt(qty.value) || 1;
            if (q > stok) qty.value = stok;

            document.getElementById('prevNama').textContent = opt.dataset.nama;
            document.getElementById('prevKat').textContent = opt.dataset.kat;
            document.getElementById('prevHarga').textContent = formatRp(harga);
            const img = document.getElementById('prevImg');
            if (opt.dataset.gambar) {
                img.src = opt.dataset.gambar;
                img.style.display = 'block';
            } else {
                img.style.display = 'none';
            }
            preview.classList.add('visible');

            document.getElementById('sumNama').textContent = opt.dataset.nama.length > 22 
                ? opt.dataset.nama.substring(0, 22) + '…' : opt.dataset.nama;
            document.getElementById('sumQty').textContent = qty.value;
            document.getElementById('sumHarga').textContent = formatRp(harga);
            document.getElementById('sumTotal').textContent = formatRp(harga * (parseInt(qty.value) || 1));
        }

        function toggleBukti() {
            const checked = document.querySelector('input[name="metode_pembayaran"]:checked');
            if (checked && checked.value !== 'Tunai') {
                buktiSection.classList.add('visible');
            } else {
                buktiSection.classList.remove('visible');
            }
        }

        sel.addEventListener('change', update);
        qty.addEventListener('input', update);
        metodeRadios.forEach(r => r.addEventListener('change', toggleBukti));
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileName.textContent = this.files[0].name;
                uploadPreview.classList.add('visible');
            } else {
                uploadPreview.classList.remove('visible');
            }
        });

        // Drag & drop
        ['dragenter','dragover'].forEach(ev => {
            uploadZone.addEventListener(ev, e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
        });
        ['dragleave','drop'].forEach(ev => {
            uploadZone.addEventListener(ev, e => { e.preventDefault(); uploadZone.classList.remove('dragover'); });
        });
        uploadZone.addEventListener('drop', e => {
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        update();
        toggleBukti();
    })();
    </script>
    <script src="js/script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
