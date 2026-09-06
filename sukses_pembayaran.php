<?php
require_once 'includes/auth.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: " . (hasRole('pembeli') ? 'produk.php' : 'penjualan.php'));
    exit;
}

$stmt = mysqli_prepare($conn, "
    SELECT p.*, u.nama_lengkap 
    FROM penjualan p 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.id = ?
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$header = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$header) {
    header("Location: " . (hasRole('pembeli') ? 'produk.php' : 'penjualan.php'));
    exit;
}

// Detail items
$details = mysqli_query($conn, "
    SELECT d.*, pr.nama_produk, pr.gambar
    FROM detail_penjualan d
    JOIN produk pr ON d.produk_id = pr.id
    WHERE d.penjualan_id = $id
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pembayaran Berhasil - Digital Electronic Ridho</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .success-page {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(160deg, #f0fdf4 0%, #ecfdf5 40%, #f0f9ff 100%);
        }
        .success-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(22, 163, 74, 0.12), 0 4px 16px rgba(0,0,0,0.06);
            max-width: 520px;
            width: 100%;
            padding: 48px 40px 36px;
            text-align: center;
            animation: popIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.85) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .check-circle {
            width: 96px;
            height: 96px;
            margin: 0 auto 24px;
            background: linear-gradient(145deg, #22c55e, #16a34a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 28px rgba(22, 163, 74, 0.35);
            animation: checkPulse 1.2s ease-in-out 0.3s both;
        }
        @keyframes checkPulse {
            0% { transform: scale(0.6); opacity: 0; }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .check-circle svg {
            width: 48px;
            height: 48px;
            stroke: #fff;
            stroke-width: 3.5;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .check-circle svg path {
            stroke-dasharray: 60;
            stroke-dashoffset: 60;
            animation: drawCheck 0.5s ease-out 0.6s forwards;
        }
        @keyframes drawCheck {
            to { stroke-dashoffset: 0; }
        }
        .success-card h1 {
            font-size: 1.6rem;
            color: #166534;
            margin: 0 0 8px;
            font-weight: 800;
        }
        .success-card .sub {
            color: #64748b;
            font-size: 0.98rem;
            margin-bottom: 28px;
            line-height: 1.5;
        }
        .info-box {
            background: #f8fafc;
            border-radius: 14px;
            padding: 18px 20px;
            text-align: left;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row span { color: #64748b; }
        .info-row strong { color: #1e1b4b; }
        .info-row.total strong {
            color: #16a34a;
            font-size: 1.15rem;
        }
        .item-list {
            text-align: left;
            margin-bottom: 20px;
        }
        .item-list h4 {
            font-size: 0.85rem;
            color: #94a3b8;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .item-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .item-row:last-child { border-bottom: none; }
        .item-row img {
            width: 48px; height: 48px;
            object-fit: cover;
            border-radius: 8px;
            background: #e2e8f0;
        }
        .item-row .name { flex: 1; font-size: 0.9rem; color: #334155; font-weight: 600; }
        .item-row .qty { color: #94a3b8; font-size: 0.85rem; }
        .item-row .sub { font-weight: 700; color: #1e1b4b; font-size: 0.9rem; }
        .bukti-thumb {
            margin: 12px 0 20px;
            text-align: left;
        }
        .bukti-thumb a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            color: #166534;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
        }
        .bukti-thumb a:hover { background: #dcfce7; }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .actions .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-primary-s {
            background: linear-gradient(135deg, #3b82f6, #7c3aed);
            color: #fff;
        }
        .btn-outline-s {
            background: #fff;
            color: #475569;
            border: 1.5px solid #e2e8f0;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
            color: #166534;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .status-badge .dot {
            width: 8px; height: 8px;
            background: #22c55e;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="success-page">
        <div class="success-card">
            <div class="check-circle">
                <svg viewBox="0 0 52 52">
                    <path d="M14 27 l8 8 l16 -18"/>
                </svg>
            </div>

            <div class="status-badge">
                <span class="dot"></span> Status: Lunas
            </div>

            <h1>Pembayaran Berhasil!</h1>
            <p class="sub">Terima kasih. Transaksi #<?= $id; ?> telah berhasil diproses dan stok produk telah diperbarui.</p>

            <div class="info-box">
                <div class="info-row">
                    <span>No. Transaksi</span>
                    <strong>#<?= str_pad($id, 6, '0', STR_PAD_LEFT); ?></strong>
                </div>
                <div class="info-row">
                    <span>Tanggal</span>
                    <strong><?= date('d M Y, H:i', strtotime($header['tanggal'])); ?></strong>
                </div>
                <div class="info-row">
                    <span>Metode</span>
                    <strong><?= htmlspecialchars($header['metode_pembayaran'] ?? 'Tunai'); ?></strong>
                </div>
                <div class="info-row">
                    <span>Kasir / Pembeli</span>
                    <strong><?= htmlspecialchars($header['nama_lengkap'] ?? '-'); ?></strong>
                </div>
                <div class="info-row total">
                    <span>Total Dibayar</span>
                    <strong><?= formatRupiah($header['total']); ?></strong>
                </div>
            </div>

            <?php if (mysqli_num_rows($details) > 0): ?>
            <div class="item-list">
                <h4>Item Dibeli</h4>
                <?php while ($d = mysqli_fetch_assoc($details)): ?>
                <div class="item-row">
                    <?php if (!empty($d['gambar'])): ?>
                        <img src="<?= htmlspecialchars($d['gambar']); ?>" alt="">
                    <?php else: ?>
                        <div style="width:48px;height:48px;background:#e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:center;">📦</div>
                    <?php endif; ?>
                    <div class="name"><?= htmlspecialchars($d['nama_produk']); ?></div>
                    <div class="qty">x<?= $d['qty']; ?></div>
                    <div class="sub"><?= formatRupiah($d['subtotal']); ?></div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($header['bukti_pembayaran'])): ?>
            <div class="bukti-thumb">
                <a href="<?= htmlspecialchars($header['bukti_pembayaran']); ?>" target="_blank">
                    📎 Lihat Bukti Pembayaran
                </a>
            </div>
            <?php endif; ?>

            <div class="actions">
                <a href="detail_penjualan.php?id=<?= $id; ?>" class="btn btn-outline-s">📄 Detail Transaksi</a>
                <?php if (hasRole('pembeli')): ?>
                    <a href="produk.php" class="btn btn-primary-s">🛍️ Belanja Lagi</a>
                <?php else: ?>
                    <a href="penjualan.php" class="btn btn-primary-s">📋 Daftar Penjualan</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
