<?php
// Digital Electronic - Konfigurasi Database
// Support Railway + Local (XAMPP/Laragon)

// Coba baca dari environment Railway
$host     = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'localhost';
$user     = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$database = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'db_digital_ridho';
$port     = (int)(getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: 3306);

// Alternatif: parse DATABASE_URL (format Railway/Heroku)
$databaseUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL') ?: '';
if ($databaseUrl && ($host === 'localhost' || empty(getenv('MYSQLHOST')))) {
    $parts = parse_url($databaseUrl);
    if ($parts) {
        $host     = $parts['host'] ?? $host;
        $port     = $parts['port'] ?? $port;
        $user     = $parts['user'] ?? $user;
        $password = $parts['pass'] ?? $password;
        $database = ltrim($parts['path'] ?? '', '/') ?: $database;
    }
}

$conn = @mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    // Tampilkan error yang jelas (hapus di production nanti)
    die(
        "Koneksi database gagal!<br>" .
        "Host: " . htmlspecialchars($host) . "<br>" .
        "User: " . htmlspecialchars($user) . "<br>" .
        "Database: " . htmlspecialchars($database) . "<br>" .
        "Port: " . $port . "<br>" .
        "Error: " . mysqli_connect_error() . "<br><br>" .
        "→ Pastikan variable MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT " .
        "sudah di-reference dari service MySQL ke service web di Railway."
    );
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
