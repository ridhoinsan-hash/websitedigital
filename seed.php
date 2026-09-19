<?php
require_once 'includes/config.php';

echo "<h2>Isi Data Contoh</h2>";

// ========== PRODUK ==========
$produk = [
    ['Laptop ASUS VivoBook 14 OLED', 'Laptop', 9250000, 18, '14 inch OLED, Intel i5, RAM 16GB, SSD 512GB', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop'],
    ['Laptop Lenovo IdeaPad Slim 5', 'Laptop', 8750000, 22, '14 inch, Ryzen 5, RAM 16GB, SSD 512GB', 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&h=300&fit=crop'],
    ['Laptop Acer Aspire 5', 'Laptop', 7890000, 15, '15.6 inch FHD, Intel i5, RAM 8GB, SSD 512GB', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400&h=300&fit=crop'],
    ['Laptop HP Pavilion 15', 'Laptop', 8990000, 12, '15.6 inch, Intel i7, RAM 16GB, SSD 512GB', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop'],
    ['Laptop ASUS ROG Zephyrus G14', 'Laptop', 18990000, 8, '14 inch QHD 165Hz, Ryzen 9, RTX 4060', 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=300&fit=crop'],
    ['MacBook Air M2 13', 'Laptop', 16499000, 9, 'Chip Apple M2, 8GB RAM, 256GB SSD', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=300&fit=crop'],
    ['Samsung Galaxy A55 5G', 'Smartphone', 5499000, 30, '6.6 inch AMOLED, 8GB/256GB, 50MP', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop'],
    ['Xiaomi Redmi Note 13 Pro', 'Smartphone', 4299000, 35, '6.67 inch AMOLED 120Hz, 12GB/512GB', 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400&h=300&fit=crop'],
    ['iPhone 15 128GB', 'Smartphone', 13999000, 15, '6.1 inch Super Retina, A16 Bionic', 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400&h=300&fit=crop'],
    ['OPPO Reno11 5G', 'Smartphone', 5999000, 22, '6.7 inch AMOLED, 12GB/256GB', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop'],
    ['Samsung Galaxy S24', 'Smartphone', 12999000, 12, '6.2 inch Dynamic AMOLED, Snapdragon 8 Gen 3', 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=300&fit=crop'],
    ['iPad Air M2 11 inch', 'Tablet', 9999000, 12, 'Chip M2, Liquid Retina, 128GB', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=300&fit=crop'],
    ['Samsung Galaxy Tab S9 FE', 'Tablet', 6499000, 15, '10.9 inch, S Pen included', 'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=400&h=300&fit=crop'],
    ['Monitor LG UltraGear 27', 'Monitor', 3899000, 16, '27 inch QHD 144Hz, 1ms', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop'],
    ['Monitor Samsung Odyssey G5 32', 'Monitor', 4299000, 10, '32 inch QHD 165Hz Curved', 'https://images.unsplash.com/photo-1586210579191-33b45e38dd2f?w=400&h=300&fit=crop'],
    ['Sony WH-1000XM5', 'Audio', 4999000, 14, 'Noise Cancelling Wireless', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop'],
    ['AirPods Pro 2', 'Audio', 3999000, 20, 'ANC, USB-C Case', 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=400&h=300&fit=crop'],
    ['SSD Samsung 990 PRO 1TB', 'Storage', 1899000, 40, 'PCIe 4.0 NVMe', 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=300&fit=crop'],
    ['Mouse Logitech MX Master 3S', 'Aksesoris', 1299000, 35, 'Wireless Ergonomic', 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop'],
    ['Keyboard Keychron K2', 'Aksesoris', 1450000, 28, 'Mechanical Wireless RGB', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&h=300&fit=crop'],
];

mysqli_query($conn, "DELETE FROM detail_penjualan");
mysqli_query($conn, "DELETE FROM penjualan");
mysqli_query($conn, "DELETE FROM pengeluaran");
mysqli_query($conn, "DELETE FROM produk");

$stmt = mysqli_prepare($conn, "INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi, gambar) VALUES (?,?,?,?,?,?)");
$okProduk = 0;
foreach ($produk as $p) {
    mysqli_stmt_bind_param($stmt, "ssdiss", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5]);
    if (mysqli_stmt_execute($stmt)) $okProduk++;
}
mysqli_stmt_close($stmt);
echo "Produk: $okProduk OK<br>";

// ========== PENJUALAN ==========
$adminId = 1;
$penjualan = [
    [1, 14748000, 'Retail 2 item', 'Transfer Bank', 'lunas'],
    [2, 8990000, 'HP Pavilion', 'Tunai', 'lunas'],
    [3, 5499000, 'Samsung A55', 'QRIS', 'lunas'],
    [4, 1299000, 'Mouse MX Master', 'E-Wallet', 'lunas'],
    [5, 18990000, 'ROG Zephyrus', 'Transfer Bank', 'lunas'],
    [6, 4299000, 'Redmi Note 13', 'Tunai', 'lunas'],
    [7, 9999000, 'iPad Air M2', 'Kartu Debit', 'lunas'],
    [8, 3899000, 'Monitor LG', 'QRIS', 'lunas'],
    [9, 4999000, 'Sony WH-1000XM5', 'E-Wallet', 'lunas'],
    [10, 3349000, 'SSD + Keyboard', 'Tunai', 'lunas'],
];

$stmt = mysqli_prepare($conn, "INSERT INTO penjualan (tanggal, total, keterangan, metode_pembayaran, status, user_id) VALUES (DATE_SUB(NOW(), INTERVAL ? DAY), ?, ?, ?, ?, ?)");
$okJual = 0;
foreach ($penjualan as $i => $pj) {
    $hari = $i + 1;
    mysqli_stmt_bind_param($stmt, "idsssi", $hari, $pj[1], $pj[2], $pj[3], $pj[4], $adminId);
    if (mysqli_stmt_execute($stmt)) $okJual++;
}
mysqli_stmt_close($stmt);
echo "Penjualan: $okJual OK<br>";

// Detail penjualan (produk_id menyesuaikan urutan insert)
$details = [
    [1, 1, 1, 9250000],   // VivoBook
    [1, 7, 1, 5499000],   // A55
    [2, 4, 1, 8990000],   // Pavilion
    [3, 7, 1, 5499000],   // A55
    [4, 19, 1, 1299000],  // Mouse
    [5, 5, 1, 18990000],  // ROG
    [6, 8, 1, 4299000],   // Redmi
    [7, 12, 1, 9999000],  // iPad
    [8, 14, 1, 3899000],  // Monitor LG
    [9, 16, 1, 4999000],  // Sony
    [10, 18, 1, 1899000], // SSD
    [10, 20, 1, 1450000], // Keyboard
];

$stmt = mysqli_prepare($conn, "INSERT INTO detail_penjualan (penjualan_id, produk_id, qty, harga_satuan, subtotal) VALUES (?,?,?,?,?)");
$okDetail = 0;
foreach ($details as $d) {
    $sub = $d[2] * $d[3];
    mysqli_stmt_bind_param($stmt, "iiidi", $d[0], $d[1], $d[2], $d[3], $sub);
    if (mysqli_stmt_execute($stmt)) $okDetail++;
}
mysqli_stmt_close($stmt);
echo "Detail penjualan: $okDetail OK<br>";

// ========== PENGELUARAN ==========
$pengeluaran = [
    [0, 'Operasional', 'Listrik & air toko', 1250000],
    [0, 'Gaji', 'Gaji kasir & staff', 8500000],
    [2, 'Persediaan', 'Restock smartphone', 18500000],
    [5, 'Marketing', 'Iklan Meta + Google', 2750000],
    [7, 'Sewa', 'Sewa ruko bulan ini', 7500000],
    [10, 'Operasional', 'Internet & telepon', 650000],
    [12, 'Maintenance', 'Service AC', 850000],
    [15, 'Persediaan', 'Pembelian monitor & laptop', 32000000],
    [18, 'Gaji', 'Bonus karyawan', 2500000],
    [20, 'Marketing', 'Banner & promo offline', 1200000],
];

$stmt = mysqli_prepare($conn, "INSERT INTO pengeluaran (tanggal, kategori, keterangan, jumlah, user_id) VALUES (DATE_SUB(CURDATE(), INTERVAL ? DAY), ?, ?, ?, ?)");
$okKeluar = 0;
foreach ($pengeluaran as $pg) {
    mysqli_stmt_bind_param($stmt, "issdi", $pg[0], $pg[1], $pg[2], $pg[3], $adminId);
    if (mysqli_stmt_execute($stmt)) $okKeluar++;
}
mysqli_stmt_close($stmt);
echo "Pengeluaran: $okKeluar OK<br>";

echo "<br><b>Selesai!</b> Hapus file seed.php, lalu refresh dashboard.";
mysqli_close($conn);
?>
