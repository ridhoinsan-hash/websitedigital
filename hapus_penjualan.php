<?php
require_once 'includes/auth.php';
requireRole(['admin', 'kasir']);

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: penjualan.php");
    exit;
}

// Ambil detail untuk restore stok
$details = mysqli_query($conn, "SELECT produk_id, qty FROM detail_penjualan WHERE penjualan_id = $id");

mysqli_begin_transaction($conn);
try {
    // Restore stok
    while ($d = mysqli_fetch_assoc($details)) {
        $stmt = mysqli_prepare($conn, "UPDATE produk SET stok = stok + ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $d['qty'], $d['produk_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Hapus penjualan (detail ikut cascade)
    $stmt = mysqli_prepare($conn, "DELETE FROM penjualan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($conn);
    header("Location: penjualan.php?pesan=sukses_hapus");
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: penjualan.php");
    exit;
}
?>
