<?php
require_once __DIR__ . '/config.php';

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Wajib login — redirect ke login.php jika belum
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Ambil data user yang sedang login
 */
function currentUser() {
    return [
        'id'       => $_SESSION['user_id'] ?? null,
        'nama'     => $_SESSION['nama_lengkap'] ?? 'User',
        'username' => $_SESSION['username'] ?? '',
        'role'     => $_SESSION['role'] ?? 'pembeli',
    ];
}

/**
 * Cek apakah user punya role tertentu
 */
function hasRole($roles) {
    if (!isLoggedIn()) return false;
    $userRole = $_SESSION['role'] ?? '';
    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }
    return $userRole === $roles;
}

/**
 * Wajib punya role tertentu
 */
function requireRole($roles) {
    requireLogin();
    if (!hasRole($roles)) {
        $role = $_SESSION['role'] ?? 'pembeli';
        if ($role === 'pembeli') {
            header("Location: produk.php");
        } else {
            header("Location: index.php");
        }
        exit;
    }
}

/**
 * Label role untuk tampilan
 */
function roleLabel($role) {
    $labels = [
        'admin'   => 'Administrator',
        'kasir'   => 'Kasir',
        'staff'   => 'Staff',
        'pembeli' => 'Pembeli',
    ];
    return $labels[$role] ?? $role;
}

/**
 * Badge class untuk role
 */
function roleBadgeClass($role) {
    $map = [
        'admin'   => 'badge-admin',
        'kasir'   => 'badge-kasir',
        'staff'   => 'badge-staff',
        'pembeli' => 'badge-pembeli',
    ];
    return $map[$role] ?? 'badge-pembeli';
}
?>
