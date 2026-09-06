<?php
require_once 'includes/auth.php';
requireRole(['admin']);

$id = (int)($_GET['id'] ?? 0);

if ($id < 1) {
    header("Location: karyawan.php");
    exit;
}

// Tidak boleh hapus diri sendiri
if ($id == currentUser()['id']) {
    header("Location: karyawan.php?error=" . urlencode("Anda tidak dapat menghapus akun sendiri."));
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    header("Location: karyawan.php?success=" . urlencode("Karyawan berhasil dihapus."));
} else {
    header("Location: karyawan.php?error=" . urlencode("Gagal menghapus karyawan."));
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
