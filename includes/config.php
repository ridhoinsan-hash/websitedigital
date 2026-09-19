<?php
// Digital Electronic - Konfigurasi Database
// Sementara hardcode dari Railway MySQL (nanti bisa diganti ke env)

$host     = 'mysql.railway.internal';
$user     = 'root';
$password = 'cDfaLukPyywJKDUKSghJRUQIaNIReVB0';
$database = 'railway';
$port     = 3306;

// Kalau env sudah ter-set, utamakan env
if (getenv('MYSQL_URL')) {
    $p = parse_url(getenv('MYSQL_URL'));
    if ($p) {
        $host     = $p['host'] ?? $host;
        $port     = isset($p['port']) ? (int)$p['port'] : $port;
        $user     = !empty($p['user']) ? $p['user'] : $user;
        $password = !empty($p['pass']) ? $p['pass'] : $password;
        if (!empty($p['path'])) {
            $database = ltrim($p['path'], '/');
        }
    }
}

if (getenv('MYSQL_ROOT_PASSWORD')) {
    $password = getenv('MYSQL_ROOT_PASSWORD');
}
if (getenv('MYSQL_DATABASE')) {
    $database = getenv('MYSQL_DATABASE');
}
if (getenv('MYSQLHOST') || getenv('MYSQL_HOST')) {
    $host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');
}

$conn = @mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    http_response_code(500);
    echo "<h2>Koneksi Database Gagal</h2>";
    echo "<pre>";
    echo "Host     : " . htmlspecialchars($host) . "\n";
    echo "User     : " . htmlspecialchars($user) . "\n";
    echo "Database : " . htmlspecialchars($database) . "\n";
    echo "Port     : " . $port . "\n";
    echo "Error    : " . mysqli_connect_error() . "\n";
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
