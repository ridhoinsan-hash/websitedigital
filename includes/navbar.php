<?php
$current = basename($_SERVER['PHP_SELF']);
$user = currentUser();
$role = $user['role'];
?>
<nav class="app-nav">
    <div class="nav-top">
        <a href="<?= $role === 'pembeli' ? 'produk.php' : 'index.php'; ?>" class="brand">
            <span class="brand-icon">⚡</span>
            <span class="brand-text">Digital Electronic Ridho</span>
        </a>
        <button type="button" class="nav-toggle" id="navToggle" aria-label="Menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
    <div class="nav-menu" id="navMenu">
        <div class="nav-links">
            <?php if (in_array($role, ['admin', 'kasir', 'staff'])): ?>
                <a href="index.php" class="<?= $current === 'index.php' ? 'active' : ''; ?>">Dashboard</a>
            <?php endif; ?>

            <a href="produk.php" class="<?= in_array($current, ['produk.php','tambah_produk.php','edit_produk.php']) ? 'active' : ''; ?>">
                <?= $role === 'pembeli' ? 'Katalog' : 'Produk'; ?>
            </a>

            <?php if (in_array($role, ['admin', 'kasir'])): ?>
                <a href="penjualan.php" class="<?= in_array($current, ['penjualan.php','tambah_penjualan.php','detail_penjualan.php','sukses_pembayaran.php']) ? 'active' : ''; ?>">Penjualan</a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <a href="pengeluaran.php" class="<?= in_array($current, ['pengeluaran.php','tambah_pengeluaran.php']) ? 'active' : ''; ?>">Pengeluaran</a>
                <a href="karyawan.php" class="<?= in_array($current, ['karyawan.php','tambah_karyawan.php','edit_karyawan.php']) ? 'active' : ''; ?>">Karyawan</a>
            <?php endif; ?>

            <?php if ($role === 'pembeli'): ?>
                <a href="tambah_penjualan.php" class="<?= $current === 'tambah_penjualan.php' ? 'active' : ''; ?>">Checkout</a>
            <?php endif; ?>

            <a href="profil.php" class="<?= $current === 'profil.php' ? 'active' : ''; ?>">Profil</a>
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
