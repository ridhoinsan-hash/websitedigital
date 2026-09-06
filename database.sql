-- =====================================================
-- Digital Electronic Ridho - Updated Database
-- Database: db_digital_ridho
-- 50 Produk + CRUD Karyawan + Data Pemasukan/Pengeluaran
-- =====================================================

CREATE DATABASE IF NOT EXISTS db_digital_ridho;
USE db_digital_ridho;

-- ===================== USERS / KARYAWAN =====================
DROP TABLE IF EXISTS detail_penjualan;
DROP TABLE IF EXISTS penjualan;
DROP TABLE IF EXISTS pengeluaran;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    role ENUM('admin','kasir','staff','pembeli') NOT NULL DEFAULT 'pembeli',
    status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Password: admin123, kasir123, staff123, pembeli123 (bcrypt)
INSERT INTO users (username, password, nama_lengkap, email, no_hp, role, status) VALUES
('admin',   '$2y$10$b8MPhOR1.2C0gZmzoK2odu3UzxBQTxvdyiP.ILnrlK39uALnNNqsG', 'Administrator Utama', 'admin@digitalridho.com', '081234567890', 'admin', 'aktif'),
('kasir',   '$2y$10$CcrUh0UavP7xjMZlqqRLEewv5.lVIcy2KLgqBfjjvW38wMcAliXqu', 'Budi Santoso', 'budi@digitalridho.com', '081234567891', 'kasir', 'aktif'),
('kasir2',  '$2y$10$CcrUh0UavP7xjMZlqqRLEewv5.lVIcy2KLgqBfjjvW38wMcAliXqu', 'Siti Rahayu', 'siti@digitalridho.com', '081234567892', 'kasir', 'aktif'),
('staff1',  '$2y$10$rXPpd.67pMkypa/PKLocRuVszT3N29o.x/GPT/9bkRIhglUmnMxoi', 'Andi Wijaya', 'andi@digitalridho.com', '081234567893', 'staff', 'aktif'),
('staff2',  '$2y$10$rXPpd.67pMkypa/PKLocRuVszT3N29o.x/GPT/9bkRIhglUmnMxoi', 'Dewi Lestari', 'dewi@digitalridho.com', '081234567894', 'staff', 'aktif'),
('pembeli', '$2y$10$rXPpd.67pMkypa/PKLocRuVszT3N29o.x/GPT/9bkRIhglUmnMxoi', 'Pelanggan Demo', 'pelanggan@email.com', '081234567895', 'pembeli', 'aktif');

-- ===================== PRODUK (50 item) =====================
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(120) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi, gambar) VALUES
('Laptop ASUS VivoBook 14 OLED', 'Laptop', 9250000.00, 18, '14 inch OLED, Intel Core i5-1235U, RAM 16GB, SSD 512GB, Windows 11', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop'),
('Laptop Lenovo IdeaPad Slim 5', 'Laptop', 8750000.00, 22, '14 inch, AMD Ryzen 5 7530U, RAM 16GB, SSD 512GB, Windows 11', 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&h=300&fit=crop'),
('Laptop Acer Aspire 5', 'Laptop', 7890000.00, 15, '15.6 inch FHD, Intel i5-1235U, RAM 8GB, SSD 512GB', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400&h=300&fit=crop'),
('Laptop HP Pavilion 15', 'Laptop', 8990000.00, 12, '15.6 inch, Intel i7-1255U, RAM 16GB, SSD 512GB, Windows 11', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop'),
('Laptop Dell Inspiron 14', 'Laptop', 9500000.00, 10, '14 inch 2-in-1, Intel i5, RAM 16GB, SSD 512GB, Touchscreen', 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400&h=300&fit=crop'),
('Laptop ASUS ROG Zephyrus G14', 'Laptop', 18990000.00, 8, '14 inch QHD 165Hz, Ryzen 9, RTX 4060, RAM 16GB, SSD 1TB', 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=300&fit=crop'),
('Laptop MacBook Air M2 13', 'Laptop', 16499000.00, 9, 'Chip Apple M2, 8GB RAM, 256GB SSD, Retina Display, macOS', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=300&fit=crop'),
('Laptop Lenovo ThinkPad E14', 'Laptop', 11250000.00, 11, '14 inch, Intel i5-1335U, RAM 16GB, SSD 512GB, Business Grade', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400&h=300&fit=crop'),
('Laptop MSI Modern 14', 'Laptop', 8490000.00, 14, '14 inch, Ryzen 5, RAM 16GB, SSD 512GB, Lightweight', 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&h=300&fit=crop'),
('Laptop Axioo MyBook 14', 'Laptop', 5990000.00, 20, '14 inch, Intel N100, RAM 8GB, SSD 256GB, Entry Level', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop'),
('Smartphone Samsung Galaxy A55 5G', 'Smartphone', 5499000.00, 30, '6.6 inch Super AMOLED, 8GB/256GB, 50MP OIS, IP67', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop'),
('Smartphone Xiaomi Redmi Note 13 Pro', 'Smartphone', 4299000.00, 35, '6.67 inch AMOLED 120Hz, 12GB/512GB, 200MP Camera', 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400&h=300&fit=crop'),
('Smartphone iPhone 15 128GB', 'Smartphone', 13999000.00, 15, '6.1 inch Super Retina XDR, A16 Bionic, Dual Camera 48MP', 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400&h=300&fit=crop'),
('Smartphone OPPO Reno11 5G', 'Smartphone', 5999000.00, 22, '6.7 inch AMOLED, 12GB/256GB, 50MP Portrait, 67W Charge', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop'),
('Smartphone Vivo V30 5G', 'Smartphone', 5799000.00, 18, '6.78 inch AMOLED, 12GB/256GB, 50MP ZEISS, Aura Light', 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400&h=300&fit=crop'),
('Smartphone realme 12 Pro+', 'Smartphone', 4999000.00, 25, '6.7 inch Curved AMOLED, 12GB/512GB, 64MP Periscope', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop'),
('Smartphone Samsung Galaxy S24', 'Smartphone', 12999000.00, 12, '6.2 inch Dynamic AMOLED 2X, 8GB/256GB, Snapdragon 8 Gen 3', 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=300&fit=crop'),
('Smartphone Google Pixel 8a', 'Smartphone', 6999000.00, 10, '6.1 inch OLED, Tensor G3, 8GB/128GB, Best Camera AI', 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400&h=300&fit=crop'),
('Smartphone Infinix Note 40 Pro', 'Smartphone', 3499000.00, 28, '6.78 inch AMOLED 120Hz, 8GB/256GB, 100W Charge', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop'),
('Smartphone Tecno Camon 30 Pro', 'Smartphone', 3999000.00, 20, '6.78 inch AMOLED, 12GB/256GB, 50MP Portrait', 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400&h=300&fit=crop'),
('Smartphone Motorola Edge 50 Fusion', 'Smartphone', 4499000.00, 16, '6.7 inch pOLED 144Hz, 8GB/256GB, IP68, 68W', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop'),
('Smartphone ASUS Zenfone 11 Ultra', 'Smartphone', 11999000.00, 8, '6.78 inch AMOLED 144Hz, Snapdragon 8 Gen 3, 16GB/512GB', 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=300&fit=crop'),
('iPad Air M2 11 inch 128GB', 'Tablet', 9999000.00, 12, 'Chip Apple M2, Liquid Retina, Apple Pencil Pro support', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=300&fit=crop'),
('Samsung Galaxy Tab S9 FE', 'Tablet', 6499000.00, 15, '10.9 inch LCD, Exynos 1380, 6GB/128GB, S Pen included', 'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=400&h=300&fit=crop'),
('Xiaomi Pad 6', 'Tablet', 4299000.00, 18, '11 inch 144Hz, Snapdragon 870, 8GB/256GB', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=300&fit=crop'),
('Lenovo Tab P11 Gen 2', 'Tablet', 3499000.00, 14, '11.5 inch 2K, MediaTek Helio G99, 4GB/128GB', 'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=400&h=300&fit=crop'),
('iPad 10th Gen 64GB', 'Tablet', 5999000.00, 20, '10.9 inch Liquid Retina, A14 Bionic, USB-C', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=300&fit=crop'),
('Monitor LG UltraGear 27 inch 144Hz', 'Monitor', 3899000.00, 16, '27 inch QHD IPS, 1ms, FreeSync Premium, HDR10', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop'),
('Monitor Samsung Odyssey G5 32 inch', 'Monitor', 4299000.00, 10, '32 inch QHD 165Hz, 1ms, Curved 1000R, FreeSync', 'https://images.unsplash.com/photo-1586210579191-33b45e38dd2f?w=400&h=300&fit=crop'),
('Monitor Dell S2722QC 27 inch 4K', 'Monitor', 5499000.00, 8, '27 inch 4K IPS, USB-C 65W, HDR, Built-in speakers', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop'),
('Monitor AOC 24G2 24 inch 144Hz', 'Monitor', 2199000.00, 22, '24 inch FHD IPS, 1ms, FreeSync, Gaming', 'https://images.unsplash.com/photo-1586210579191-33b45e38dd2f?w=400&h=300&fit=crop'),
('Monitor BenQ GW2480 24 inch', 'Monitor', 1899000.00, 18, '24 inch FHD IPS, Eye-care, Speakers, Office', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop'),
('Headset Sony WH-1000XM5', 'Audio', 4999000.00, 14, 'Noise Cancelling Wireless, 30 jam battery, Hi-Res Audio', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop'),
('Earphone Apple AirPods Pro 2', 'Audio', 3999000.00, 20, 'ANC, Spatial Audio, USB-C Case, Adaptive Audio', 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=400&h=300&fit=crop'),
('Headset Logitech G Pro X 2', 'Audio', 2499000.00, 12, 'Wireless Gaming, LIGHTSPEED, Blue VO!CE Mic', 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=300&fit=crop'),
('Speaker JBL Flip 6', 'Audio', 1899000.00, 25, 'Portable Bluetooth, IP67, 12 jam playtime', 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=300&fit=crop'),
('Earphone Samsung Galaxy Buds2 Pro', 'Audio', 2499000.00, 18, 'ANC, 360 Audio, IPX7, Seamless Galaxy', 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=400&h=300&fit=crop'),
('Headset Razer BlackShark V2 Pro', 'Audio', 2799000.00, 10, 'Wireless Esports, THX Spatial, 70 jam battery', 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=300&fit=crop'),
('SSD Samsung 990 PRO 1TB NVMe', 'Storage', 1899000.00, 40, 'PCIe 4.0, Baca 7450MB/s, Tulis 6900MB/s', 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=300&fit=crop'),
('SSD Kingston NV2 1TB', 'Storage', 899000.00, 45, 'PCIe 4.0 NVMe, Baca 3500MB/s', 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=300&fit=crop'),
('HDD External Seagate 2TB', 'Storage', 999000.00, 30, 'USB 3.0 Portable, Backup Software', 'https://images.unsplash.com/photo-1531492742700-e4e0e9a0a0a0?w=400&h=300&fit=crop'),
('Flashdisk SanDisk Extreme 128GB', 'Storage', 349000.00, 60, 'USB 3.2, Baca 400MB/s, IP55', 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400&h=300&fit=crop'),
('Mouse Logitech MX Master 3S', 'Aksesoris', 1299000.00, 35, 'Wireless Ergonomic, Silent Click, Multi-device', 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop'),
('Keyboard Mechanical Keychron K2', 'Aksesoris', 1450000.00, 28, 'Wireless, RGB, Hot-swappable, Mac/Windows', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&h=300&fit=crop'),
('Webcam Logitech C920 HD Pro', 'Aksesoris', 899000.00, 24, 'Full HD 1080p, Autofocus, Dual Stereo Mic', 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400&h=300&fit=crop'),
('Charger Anker 65W GaN Prime', 'Aksesoris', 549000.00, 50, 'Fast Charging USB-C PD, Compact, 3 Port', 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=300&fit=crop'),
('Powerbank Anker 20000mAh 30W', 'Aksesoris', 699000.00, 40, 'PD 30W, 2 Port, LED Display', 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=300&fit=crop'),
('Mousepad Razer Goliathus Extended', 'Aksesoris', 399000.00, 35, 'XXL Gaming, Optimized for all sensors', 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop'),
('Kabel USB-C to USB-C Anker 100W 2m', 'Aksesoris', 249000.00, 55, 'Fast Charge 100W, Braided, Durable', 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=300&fit=crop'),
('Stand Laptop Aluminium Adjustable', 'Aksesoris', 329000.00, 30, 'Ergonomic, Foldable, Heat Dissipation', 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop');

-- ===================== PENJUALAN =====================
CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(14,2) NOT NULL DEFAULT 0,
    keterangan VARCHAR(255) DEFAULT NULL,
    metode_pembayaran VARCHAR(50) DEFAULT 'Tunai',
    bukti_pembayaran VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','lunas','batal') NOT NULL DEFAULT 'lunas',
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penjualan_id INT NOT NULL,
    produk_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(14,2) NOT NULL,
    FOREIGN KEY (penjualan_id) REFERENCES penjualan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE RESTRICT
);

INSERT INTO penjualan (tanggal, total, keterangan, metode_pembayaran, status, user_id) VALUES
(DATE_SUB(NOW(), INTERVAL 1 DAY), 15498000.00, 'Penjualan retail - 2 item', 'Transfer Bank', 'lunas', 2),
(DATE_SUB(NOW(), INTERVAL 2 DAY), 8990000.00, 'Penjualan HP Pavilion', 'Tunai', 'lunas', 2),
(DATE_SUB(NOW(), INTERVAL 3 DAY), 5499000.00, 'Samsung A55', 'QRIS', 'lunas', 3),
(DATE_SUB(NOW(), INTERVAL 4 DAY), 1299000.00, 'Mouse MX Master', 'E-Wallet', 'lunas', 2),
(DATE_SUB(NOW(), INTERVAL 5 DAY), 18990000.00, 'ROG Zephyrus G14', 'Transfer Bank', 'lunas', 1),
(DATE_SUB(NOW(), INTERVAL 6 DAY), 4299000.00, 'Redmi Note 13 Pro', 'Tunai', 'lunas', 3),
(DATE_SUB(NOW(), INTERVAL 7 DAY), 9999000.00, 'iPad Air M2', 'Kartu Debit/Kredit', 'lunas', 2),
(DATE_SUB(NOW(), INTERVAL 8 DAY), 3899000.00, 'Monitor LG UltraGear', 'QRIS', 'lunas', 2),
(DATE_SUB(NOW(), INTERVAL 9 DAY), 4999000.00, 'Sony WH-1000XM5', 'E-Wallet', 'lunas', 3),
(DATE_SUB(NOW(), INTERVAL 10 DAY), 2798000.00, 'SSD + Keyboard', 'Tunai', 'lunas', 2);

INSERT INTO detail_penjualan (penjualan_id, produk_id, qty, harga_satuan, subtotal) VALUES
(1, 1, 1, 9250000.00, 9250000.00),
(1, 11, 1, 5499000.00, 5499000.00),
(2, 4, 1, 8990000.00, 8990000.00),
(3, 11, 1, 5499000.00, 5499000.00),
(4, 43, 1, 1299000.00, 1299000.00),
(5, 6, 1, 18990000.00, 18990000.00),
(6, 12, 1, 4299000.00, 4299000.00),
(7, 23, 1, 9999000.00, 9999000.00),
(8, 28, 1, 3899000.00, 3899000.00),
(9, 33, 1, 4999000.00, 4999000.00),
(10, 39, 1, 1899000.00, 1899000.00),
(10, 44, 1, 1450000.00, 1450000.00);

-- ===================== PENGELUARAN =====================
CREATE TABLE pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    jumlah DECIMAL(14,2) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO pengeluaran (tanggal, kategori, keterangan, jumlah, user_id) VALUES
(CURDATE(), 'Operasional', 'Biaya listrik & air toko bulan ini', 1250000.00, 1),
(CURDATE(), 'Gaji', 'Gaji kasir & staff (2 orang)', 8500000.00, 1),
(DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Persediaan', 'Restock smartphone & aksesoris', 18500000.00, 1),
(DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'Marketing', 'Iklan Meta Ads + Google Ads', 2750000.00, 1),
(DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'Sewa', 'Sewa ruko bulan berjalan', 7500000.00, 1),
(DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'Operasional', 'Internet & telepon toko', 650000.00, 1),
(DATE_SUB(CURDATE(), INTERVAL 12 DAY), 'Maintenance', 'Service AC & kebersihan toko', 850000.00, 1),
(DATE_SUB(CURDATE(), INTERVAL 15 DAY), 'Persediaan', 'Pembelian monitor & laptop', 32000000.00, 1),
(DATE_SUB(CURDATE(), INTERVAL 18 DAY), 'Gaji', 'Bonus kinerja karyawan', 2500000.00, 1),
(DATE_SUB(CURDATE(), INTERVAL 20 DAY), 'Marketing', 'Banner & materi promosi offline', 1200000.00, 1);
