<?php
// Digital Electronic - Konfigurasi
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
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

function getSetting($kunci, $default = '') {
    global $conn;
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $q = @mysqli_query($conn, "SELECT kunci, nilai FROM pengaturan");
        if ($q) {
            while ($row = mysqli_fetch_assoc($q)) {
                $cache[$row['kunci']] = $row['nilai'];
            }
        }
    }
    return $cache[$kunci] ?? $default;
}

function getAllSettings() {
    global $conn;
    $out = [];
    $q = @mysqli_query($conn, "SELECT kunci, nilai FROM pengaturan");
    if ($q) {
        while ($row = mysqli_fetch_assoc($q)) {
            $out[$row['kunci']] = $row['nilai'];
        }
    }
    return $out;
}

function shopName() {
    return getSetting('nama_toko', 'Digital Electronic');
}
