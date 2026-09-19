<?php
require_once 'includes/config.php';

$queries = [
"CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    role ENUM('admin','kasir','staff','pembeli') NOT NULL DEFAULT 'pembeli',
    status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)",
"CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(120) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)",
"CREATE TABLE IF NOT EXISTS penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(14,2) NOT NULL DEFAULT 0,
    keterangan VARCHAR(255) DEFAULT NULL,
    metode_pembayaran VARCHAR(50) DEFAULT 'Tunai',
    bukti_pembayaran VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','lunas','batal') NOT NULL DEFAULT 'lunas',
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)",
"CREATE TABLE IF NOT EXISTS detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penjualan_id INT NOT NULL,
    produk_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(14,2) NOT NULL
)",
"CREATE TABLE IF NOT EXISTS pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    jumlah DECIMAL(14,2) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)",
"CREATE TABLE IF NOT EXISTS pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kunci VARCHAR(50) NOT NULL UNIQUE,
    nilai TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)",
"INSERT IGNORE INTO users (username, password, nama_lengkap, email, no_hp, role, status) VALUES
('admin', '\$2y\$10\$b8MPhOR1.2C0gZmzoK2odu3UzxBQTxvdyiP.ILnrlK39uALnNNqsG', 'Administrator Utama', 'admin@digitalridho.com', '081234567890', 'admin', 'aktif')",
"INSERT IGNORE INTO pengaturan (kunci, nilai) VALUES
('nama_toko', 'Digital Electronic'),
('tipe_toko', 'Toko Elektronik'),
('alamat', 'Indonesia'),
('telepon', '0812-0000-0000'),
('footer_struk', 'Terima kasih telah berbelanja')"
];

echo "<h2>Install Database</h2>";
foreach ($queries as $i => $sql) {
    if (mysqli_query($conn, $sql)) {
        echo ($i + 1) . ". OK<br>";
    } else {
        echo ($i + 1) . ". ERROR: " . mysqli_error($conn) . "<br>";
    }
}
echo "<br><b>Selesai.</b> Hapus file install.php, lalu login: admin / admin123";
mysqli_close($conn);
?>
