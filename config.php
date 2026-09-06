<?php
// Konfigurasi Database - Digital Electronic Ridho
$host     = "localhost";
$user     = "root";
$password = "";          // Kosongkan jika XAMPP default
$database = "db_digital_ridho";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>
