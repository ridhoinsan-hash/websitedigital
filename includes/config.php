<?php
// Digital Electronic - Konfigurasi Database (Railway)
// Baca dari MYSQL_URL / MYSQL_ROOT_PASSWORD yang tersedia di service MySQL

$host     = 'localhost';
$user     = 'root';
$password = '';
$database = 'railway';
$port     = 3306;

// 1. Prioritas: parse MYSQL_URL (paling lengkap)
$mysqlUrl = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';
if (!empty($mysqlUrl)) {
    $p = parse_url($mysqlUrl);
    if ($p !== false) {
        $host     = $p['host'] ?? $host;
        $port     = isset($p['port']) ? (int)$p['port'] : $port;
        $user     = $p['user'] ?? $user;
        $password = $p['pass'] ?? $password;
        if (!empty($p['path'])) {
            $database = ltrim($p['path'], '/');
        }
    }
}

// 2. Fallback dari variable terpisah (yang ada di Railway kamu)
$host     = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: $host;
$user     = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: $user;
$password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: $password;
$database = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: $database;
$port     = (int)(getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: $port);

// 3. Railway internal biasanya user = root
if ($user === '' || $user === null) {
    $user = 'root';
}
if ($database === '' || $database === null) {
    $database = 'railway';
}

$conn = @mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    http_response_code(500);
    echo "<h2>Koneksi Database Gagal</h2>";
    echo "<pre>";
    echo "Host     : " . htmlspecialchars((string)$host) . "\n";
    echo "User     : " . htmlspecialchars((string)$user) . "\n";
    echo "Database : " . htmlspecialchars((string)$database) . "\n";
    echo "Port     : " . $port . "\n";
    echo "Error    : " . mysqli_connect_error() . "\n\n";
    echo "MYSQL_URL          : " . (getenv('MYSQL_URL') ? '(ada)' : '(kosong)') . "\n";
    echo "MYSQL_ROOT_PASSWORD: " . (getenv('MYSQL_ROOT_PASSWORD') ? '(ada)' : '(kosong)') . "\n";
    echo "MYSQL_DATABASE     : " . (getenv('MYSQL_DATABASE') ?: '(kosong)') . "\n";
    echo "MYSQLHOST          : " . (getenv('MYSQLHOST') ?: '(kosong)') . "\n";
    echo "</pre>";
    exit;
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
