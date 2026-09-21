<?php
$current = basename($_SERVER['PHP_SELF']);
$user = currentUser();
$role = $user['role'];
$nama_brand = function_exists('shopName') ? shopName() : 'Digital Electronic';

$is_dashboard = ($current === 'index.php' || $current === 'portofolio.php' || $current === 'dashboard_pembeli.php');
$is_produk = in_array($current, ['produk.php','tambah_produk.php','edit_produk.php']);
$is_penjualan = in_array($current, ['penjualan.php','tambah_penjualan.php','detail_penjualan.php','sukses_pembayaran.php']);
$is_pengeluaran = in_array($current, ['pengeluaran.php','tambah_pengeluaran.php']);
$is_karyawan = in_array($current, ['karyawan.php','tambah_karyawan.php','edit_karyawan.php']);
$is_setting = ($current === 'pengaturan.php');
$is_profil = ($current === 'profil.php');
$is_porto = ($current === 'portofolio.php');
$is_dash_pembeli = ($current === 'dashboard_pembeli.php');

$cart_n = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $ci) {
        $cart_n += (int)($ci['qty'] ?? 0);
    }
}
?>
<nav class="app-nav">
    <div class="nav-top">
        <a href="<?= $role === 'pembeli' ? 'dashboard_pembeli.php' : 'index.php'; ?>" class="brand">
            <span class="brand-icon">⚡</span>
            <span class="brand-text"><?= htmlspecialchars($nama_brand); ?></span>
        </a>
        <button type="button" class="nav-toggle" id="navToggle" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
    <div class="nav-menu" id="navMenu">
        <div class="nav-links">
            <?php if (in_array($role, ['admin','kasir','staff'])): ?>
                <a href="index.php" class="<?= $current==='index.php'?'active':''; ?>">Dashboard</a>
                <a href="portofolio.php" class="<?= $is_porto?'active':''; ?>">Portofolio</a>
            <?php endif; ?>

            <?php if ($role === 'pembeli'): ?>
                <a href="dashboard_pembeli.php" class="<?= $is_dash_pembeli?'active':''; ?>">Dashboard</a>
                <a href="produk.php" class="<?= $is_produk?'active':''; ?>">Katalog</a>
                <a href="tambah_penjualan.php" class="<?= $current==='tambah_penjualan.php'?'active':''; ?>">Checkout</a>
            <?php else: ?>
                <a href="produk.php" class="<?= $is_produk?'active':''; ?>">Produk</a>
            <?php endif; ?>

            <?php if (in_array($role, ['admin','kasir'])): ?>
                <a href="penjualan.php" class="<?= $is_penjualan && $current!=='tambah_penjualan.php'?'active':''; ?>">Penjualan</a>
                <a href="tambah_penjualan.php" class="<?= $current==='tambah_penjualan.php'?'active':''; ?>">+ Kasir</a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <a href="pengeluaran.php" class="<?= $is_pengeluaran?'active':''; ?>">Pengeluaran</a>
                <a href="karyawan.php" class="<?= $is_karyawan?'active':''; ?>">Karyawan</a>
                <a href="pengaturan.php" class="<?= $is_setting?'active':''; ?>">Pengaturan</a>
            <?php endif; ?>

            <a href="profil.php" class="<?= $is_profil?'active':''; ?>">Profil</a>
            <a href="logout.php" class="nav-logout">Logout</a>
        </div>
        <div class="user-info">
            <span class="user-avatar"><?= strtoupper(substr($user['nama'], 0, 1)); ?></span>
            <span class="user-meta">
                <?= htmlspecialchars($user['nama']); ?>
                <small><?= roleLabel($role); ?></small>
            </span>
        </div>
    </div>
</nav>

<nav class="bottom-nav" aria-label="Navigasi mobile">
    <div class="bottom-nav-inner">
        <?php if (in_array($role, ['admin','kasir','staff'])): ?>
        <a href="index.php" class="<?= $current==='index.php'?'active':''; ?>">
            <span class="nav-ico">📊</span><span>Home</span>
        </a>
        <?php endif; ?>

        <?php if ($role === 'pembeli'): ?>
        <a href="dashboard_pembeli.php" class="<?= $is_dash_pembeli?'active':''; ?>">
            <span class="nav-ico">🏠</span><span>Home</span>
        </a>
        <?php endif; ?>

        <a href="produk.php" class="<?= $is_produk?'active':''; ?>">
            <span class="nav-ico">📦</span><span><?= $role==='pembeli'?'Katalog':'Produk'; ?></span>
        </a>

        <?php if (in_array($role, ['admin','kasir','pembeli'])): ?>
        <a href="tambah_penjualan.php" class="nav-center <?= $current==='tambah_penjualan.php'?'active':''; ?>">
            <span class="nav-ico">🛒</span><span><?= $role==='pembeli'?'Checkout':'Kasir'; ?></span>
            <?php if ($cart_n > 0): ?>
                <span class="bn-badge"><?= $cart_n > 9 ? '9+' : $cart_n; ?></span>
            <?php endif; ?>
        </a>
        <?php endif; ?>

        <?php if (in_array($role, ['admin','kasir'])): ?>
        <a href="penjualan.php" class="<?= $is_penjualan && $current!=='tambah_penjualan.php'?'active':''; ?>">
            <span class="nav-ico">🧾</span><span>Riwayat</span>
        </a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
        <a href="portofolio.php" class="<?= ($is_porto||$is_setting||$is_pengeluaran||$is_karyawan)?'active':''; ?>">
            <span class="nav-ico">📁</span><span>Lainnya</span>
        </a>
        <?php else: ?>
        <a href="profil.php" class="<?= $is_profil?'active':''; ?>">
            <span class="nav-ico">👤</span><span>Profil</span>
        </a>
        <?php endif; ?>
    </div>
</nav>
