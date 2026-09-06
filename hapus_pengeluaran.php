<?php
require_once 'includes/auth.php';
requireRole('admin');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: pengeluaran.php");
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM pengeluaran WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: pengeluaran.php?pesan=sukses_hapus");
exit;
?>
