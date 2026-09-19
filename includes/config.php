<?php
mysqli_report(MYSQLI_REPORT_OFF);

$host     = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$user     = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: 'CW5KbnkHvDRdcuCJPHGuBFsFsaucnm11';
$database = getenv('MYSQLDATABASE') ?: 'railway';
$port     = (int)(getenv('MYSQLPORT') ?: 3306);

$conn = @mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

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
