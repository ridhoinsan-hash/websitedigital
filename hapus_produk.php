<?php
require_once 'includes/auth.php';
requireRole('admin');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: produk.php");
    exit;
}

// Cek apakah produk pernah terjual
$cek = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM detail_penjualan WHERE produk_id = $id");
$jml = mysqli_fetch_assoc($cek)['jml'];

if ($jml > 0) {
    header("Location: produk.php?pesan=gagal_hapus");
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: produk.php?pesan=sukses_hapus");
exit;
?>
