<?php
// Konfigurasi Database - Digital Electronic Ridho
// Otomatis pakai environment Railway; fallback ke local

$host     = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
$user     = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: '';
$database = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'db_digital_ridho';
$port     = (int)(getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306);

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>
