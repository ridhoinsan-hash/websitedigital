<?php
require_once 'includes/config.php';
set_time_limit(180);

echo "<h2>Seed Lengkap — Produk, Karyawan, Transaksi, Pengeluaran</h2>";

// Bersihkan data lama (kecuali admin)
mysqli_query($conn, "DELETE FROM detail_penjualan");
mysqli_query($conn, "DELETE FROM penjualan");
mysqli_query($conn, "DELETE FROM pengeluaran");
mysqli_query($conn, "DELETE FROM produk");
mysqli_query($conn, "DELETE FROM users WHERE username != 'admin'");

// ========== KARYAWAN ==========
$karyawan = [
    ['kasir',   '$2y$10$CcrUh0UavP7xjMZlqqRLEewv5.lVIcy2KLgqBfjjvW38wMcAliXqu', 'Budi Santoso', 'budi@digitalridho.com', '081234567891', 'kasir'],
    ['kasir2',  '$2y$10$CcrUh0UavP7xjMZlqqRLEewv5.lVIcy2KLgqBfjjvW38wMcAliXqu', 'Siti Rahayu', 'siti@digitalridho.com', '081234567892', 'kasir'],
    ['staff1',  '$2y$10$rXPpd.67pMkypa/PKLocRuVszT3N29o.x/GPT/9bkRIhglUmnMxoi', 'Andi Wijaya', 'andi@digitalridho.com', '081234567893', 'staff'],
    ['staff2',  '$2y$10$rXPpd.67pMkypa/PKLocRuVszT3N29o.x/GPT/9bkRIhglUmnMxoi', 'Dewi Lestari', 'dewi@digitalridho.com', '081234567894', 'staff'],
    ['pembeli', '$2y$10$rXPpd.67pMkypa/PKLocRuVszT3N29o.x/GPT/9bkRIhglUmnMxoi', 'Pelanggan Demo', 'pelanggan@email.com', '081234567895', 'pembeli'],
    ['kasir3',  '$2y$10$CcrUh0UavP7xjMZlqqRLEewv5.lVIcy2KLgqBfjjvW38wMcAliXqu', 'Rina Marlina', 'rina@digitalridho.com', '081234567896', 'kasir'],
    ['staff3',  '$2y$10$rXPpd.67pMkypa/PKLocRuVszT3N29o.x/GPT/9bkRIhglUmnMxoi', 'Eko Prasetyo', 'eko@digitalridho.com', '081234567897', 'staff'],
];

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role, status) VALUES (?,?,?,?,?,?,'aktif')");
$okUser = 0;
foreach ($karyawan as $k) {
    mysqli_stmt_bind_param($stmt, "ssssss", $k[0], $k[1], $k[2], $k[3], $k[4], $k[5]);
    if (mysqli_stmt_execute($stmt)) $okUser++;
}
mysqli_stmt_close($stmt);
echo "Karyawan/user: $okUser OK<br>";
echo "Password: kasir/kasir2/kasir3 = <b>kasir123</b> | staff/pembeli = <b>pembeli123</b><br><br>";

// ========== PRODUK 150 ==========
$imgs = [
    'laptop' => [
        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400&h=300&fit=crop',
    ],
    'phone' => [
        'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400&h=300&fit=crop',
    ],
    'tablet' => [
        'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1585790050230-5dd28404f6b6?w=400&h=300&fit=crop',
    ],
    'monitor' => [
        'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1586210579191-33b45e38dd2f?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1614624532983-4ce03382d20e?w=400&h=300&fit=crop',
    ],
    'audio' => [
        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=300&fit=crop',
    ],
    'storage' => [
        'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1531492742700-e4e0e9a0a0a0?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400&h=300&fit=crop',
    ],
    'aksesoris' => [
        'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=300&fit=crop',
        'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400&h=300&fit=crop',
    ],
];

function imgPick($cat, $imgs, $i) {
    $arr = $imgs[$cat];
    return $arr[$i % count($arr)];
}

$produk = [];

$laptops = [
    ['ASUS VivoBook 14 OLED', 9250000, '14" OLED, i5-1235U, 16GB, 512GB SSD'],
    ['Lenovo IdeaPad Slim 5', 8750000, '14", Ryzen 5 7530U, 16GB, 512GB'],
    ['Acer Aspire 5', 7890000, '15.6" FHD, i5-1235U, 8GB, 512GB'],
    ['HP Pavilion 15', 8990000, '15.6", i7-1255U, 16GB, 512GB'],
    ['Dell Inspiron 14', 9500000, '14" 2-in-1, i5, 16GB, Touch'],
    ['ASUS ROG Zephyrus G14', 18990000, '14" QHD 165Hz, Ryzen 9, RTX 4060'],
    ['MacBook Air M2 13', 16499000, 'M2, 8GB, 256GB SSD, macOS'],
    ['Lenovo ThinkPad E14', 11250000, '14", i5-1335U, 16GB, Business'],
    ['MSI Modern 14', 8490000, '14", Ryzen 5, 16GB, Lightweight'],
    ['Axioo MyBook 14', 5990000, '14", N100, 8GB, 256GB'],
    ['ASUS TUF Gaming A15', 14990000, '15.6" 144Hz, Ryzen 7, RTX 4050'],
    ['Acer Nitro 5', 13990000, '15.6", i5, RTX 4050, 16GB'],
    ['HP Victus 16', 15490000, '16.1", i7, RTX 4060, 16GB'],
    ['Lenovo Legion 5', 16990000, '15.6", Ryzen 7, RTX 4060'],
    ['Dell XPS 13', 18990000, '13.4" OLED, i7, 16GB, Premium'],
    ['MacBook Pro 14 M3', 24990000, 'M3, 16GB, 512GB SSD'],
    ['ASUS Zenbook 14', 11990000, '14" OLED, i5, 16GB, Ultra slim'],
    ['Huawei MateBook D16', 9990000, '16", i5, 16GB, 512GB'],
    ['Microsoft Surface Laptop 5', 15990000, '13.5", i5, 8GB, Touch'],
    ['Gigabyte G5', 12990000, '15.6", i5, RTX 4050, Gaming'],
    ['ASUS ExpertBook B5', 10990000, '14", i5, 16GB, Business'],
    ['Lenovo Yoga Slim 7', 13490000, '14", Ryzen 7, 16GB, OLED'],
    ['Acer Swift 3', 8990000, '14", i5, 16GB, 512GB'],
    ['HP Envy x360', 12490000, '13.3", i5, 16GB, 2-in-1'],
    ['Dell Latitude 5440', 13990000, '14", i5, 16GB, Enterprise'],
    ['ASUS Chromebook Flip', 6490000, '14", i3, 8GB, ChromeOS'],
    ['Lenovo LOQ 15', 11990000, '15.6", Ryzen 5, RTX 4050'],
    ['MSI Katana 15', 14490000, '15.6", i7, RTX 4060'],
    ['Acer TravelMate', 7990000, '14", i5, 8GB, Office'],
    ['Axioo Pongo 725', 8990000, '15.6", i5, RTX 3050, Gaming'],
];
foreach ($laptops as $i => $p) {
    $produk[] = [$p[0], 'Laptop', $p[1], rand(5, 25), $p[2], imgPick('laptop', $imgs, $i)];
}

$phones = [
    ['Samsung Galaxy A55 5G', 5499000, '6.6" AMOLED, 8/256GB, 50MP'],
    ['Xiaomi Redmi Note 13 Pro', 4299000, '6.67" 120Hz, 12/512GB, 200MP'],
    ['iPhone 15 128GB', 13999000, '6.1" Super Retina, A16, 48MP'],
    ['OPPO Reno11 5G', 5999000, '6.7" AMOLED, 12/256GB, 50MP'],
    ['Vivo V30 5G', 5799000, '6.78" AMOLED, ZEISS Camera'],
    ['realme 12 Pro+', 4999000, '6.7" Curved, 12/512GB, Periscope'],
    ['Samsung Galaxy S24', 12999000, '6.2" Dynamic AMOLED, SD 8 Gen 3'],
    ['Google Pixel 8a', 6999000, '6.1" OLED, Tensor G3, Best AI Cam'],
    ['Infinix Note 40 Pro', 3499000, '6.78" AMOLED, 8/256GB, 100W'],
    ['Tecno Camon 30 Pro', 3999000, '6.78" AMOLED, 12/256GB'],
    ['iPhone 14 128GB', 10999000, '6.1", A15, Dual Camera'],
    ['Samsung Galaxy A35 5G', 4499000, '6.6" Super AMOLED, 8/256GB'],
    ['Xiaomi 14T', 6999000, '6.67" AMOLED, Leica, 12/256GB'],
    ['OPPO A79 5G', 3299000, '6.72" LCD, 8/256GB, 50MP'],
    ['Vivo Y36', 2999000, '6.64" LCD, 8/256GB, 50MP'],
    ['realme C67', 2499000, '6.72" LCD, 8/256GB, 108MP'],
    ['Samsung Galaxy Z Flip5', 14999000, 'Foldable, 8/256GB'],
    ['iPhone 15 Pro 128GB', 18999000, '6.1" ProMotion, A17 Pro'],
    ['Xiaomi Redmi 13C', 1799000, '6.74" LCD, 6/128GB, Entry'],
    ['Infinix Hot 40 Pro', 2499000, '6.78" LCD, 8/256GB, 108MP'],
    ['OPPO Find N3 Flip', 15999000, 'Foldable Flip, Hasselblad'],
    ['Vivo X100', 9990000, '6.78" AMOLED, ZEISS, Dimensity'],
    ['realme GT 6', 7990000, '6.78" 120Hz, SD 8s Gen 3'],
    ['Samsung Galaxy M34', 3499000, '6.5" Super AMOLED, 8/256GB'],
    ['iPhone SE 2022', 6499000, '4.7", A15, Compact'],
    ['Xiaomi Poco X6 Pro', 4499000, '6.67" 120Hz, Dimensity 8300'],
    ['OPPO Reno10', 4999000, '6.7" AMOLED, 8/256GB'],
    ['Vivo V29e', 4499000, '6.67" AMOLED, 8/256GB'],
    ['Tecno Spark 20 Pro+', 2799000, '6.78" LCD, 8/256GB'],
    ['Infinix Zero 30', 3999000, '6.78" AMOLED, 8/256GB, 108MP'],
    ['Samsung Galaxy A15', 2799000, '6.5" Super AMOLED, 6/128GB'],
    ['Xiaomi Redmi Note 12', 2999000, '6.67" AMOLED, 8/256GB'],
    ['realme Narzo 60x', 2299000, '6.72" LCD, 6/128GB'],
    ['Motorola Edge 50 Fusion', 4499000, '6.7" pOLED 144Hz, IP68'],
    ['ASUS Zenfone 11 Ultra', 11999000, '6.78" 144Hz, SD 8 Gen 3'],
];
foreach ($phones as $i => $p) {
    $produk[] = [$p[0], 'Smartphone', $p[1], rand(10, 40), $p[2], imgPick('phone', $imgs, $i)];
}

$tablets = [
    ['iPad Air M2 11 inch', 9999000, 'M2, Liquid Retina, 128GB'],
    ['Samsung Galaxy Tab S9 FE', 6499000, '10.9", S Pen, 6/128GB'],
    ['Xiaomi Pad 6', 4299000, '11" 144Hz, SD 870, 8/256GB'],
    ['iPad 10th Gen 64GB', 5999000, '10.9", A14, USB-C'],
    ['Lenovo Tab P11 Gen 2', 3499000, '11.5" 2K, G99, 4/128GB'],
    ['Samsung Galaxy Tab A9+', 3299000, '11", 4/64GB, Kids ready'],
    ['iPad Pro 11 M4', 16999000, 'M4, OLED, 256GB'],
    ['Huawei MatePad 11.5', 4499000, '11.5", Kirin, 8/128GB'],
    ['Realme Pad 2', 2999000, '11.5", Helio G99, 8/256GB'],
    ['Infinix Xpad', 2499000, '11", 4/128GB, Budget'],
    ['Lenovo Tab M10', 2299000, '10.1", 4/64GB'],
    ['Samsung Galaxy Tab S6 Lite', 4999000, '10.4", S Pen, 4/64GB'],
    ['iPad mini 6', 7990000, '8.3", A15, 64GB'],
    ['Xiaomi Pad 6 Pro', 5990000, '11" 144Hz, 8/256GB'],
    ['OPPO Pad Air', 2799000, '10.3", 4/64GB'],
];
foreach ($tablets as $i => $p) {
    $produk[] = [$p[0], 'Tablet', $p[1], rand(8, 20), $p[2], imgPick('tablet', $imgs, $i)];
}

$monitors = [
    ['LG UltraGear 27 inch 144Hz', 3899000, '27" QHD IPS, 1ms, FreeSync'],
    ['Samsung Odyssey G5 32 inch', 4299000, '32" QHD 165Hz Curved'],
    ['Dell S2722QC 27 inch 4K', 5499000, '27" 4K IPS, USB-C 65W'],
    ['AOC 24G2 24 inch 144Hz', 2199000, '24" FHD IPS, Gaming'],
    ['BenQ GW2480 24 inch', 1899000, '24" FHD IPS, Eye-care'],
    ['LG 27UP850 4K', 5990000, '27" 4K, USB-C, HDR'],
    ['MSI G274QPF 27 inch', 3499000, '27" QHD 170Hz, Rapid IPS'],
    ['ASUS TUF VG27AQ', 4199000, '27" QHD 165Hz, G-Sync'],
    ['Samsung M5 Smart 27 inch', 2990000, '27" FHD Smart Monitor'],
    ['ViewSonic VX2479', 1799000, '24" FHD 75Hz, IPS'],
    ['LG UltraWide 29 inch', 4499000, '29" WFHD IPS, Ultrawide'],
    ['Dell P2422H 24 inch', 2499000, '24" FHD IPS, Office'],
    ['Acer Nitro XV252Q', 2799000, '24.5" FHD 280Hz, Gaming'],
    ['Philips 276E 27 inch', 2299000, '27" FHD IPS, Speakers'],
    ['Gigabyte G27Q 27 inch', 3699000, '27" QHD 144Hz, KVM'],
];
foreach ($monitors as $i => $p) {
    $produk[] = [$p[0], 'Monitor', $p[1], rand(6, 18), $p[2], imgPick('monitor', $imgs, $i)];
}

$audio = [
    ['Sony WH-1000XM5', 4999000, 'ANC Wireless, 30 jam'],
    ['Apple AirPods Pro 2', 3999000, 'ANC, USB-C, Spatial'],
    ['Logitech G Pro X 2', 2499000, 'Wireless Gaming Headset'],
    ['JBL Flip 6', 1899000, 'Portable BT, IP67'],
    ['Samsung Galaxy Buds2 Pro', 2499000, 'ANC, 360 Audio'],
    ['Razer BlackShark V2 Pro', 2799000, 'Wireless Esports, THX'],
    ['Sony WF-1000XM5', 3499000, 'TWS ANC Flagship'],
    ['JBL Tune 770NC', 1499000, 'Over-ear ANC, 70 jam'],
    ['Anker Soundcore Life Q30', 999000, 'ANC, 40 jam, Budget'],
    ['Apple AirPods 3', 2499000, 'Spatial Audio, MagSafe'],
    ['Bose QuietComfort Ultra', 5499000, 'Premium ANC Headset'],
    ['HyperX Cloud III', 1299000, 'Gaming Wired, Comfort'],
    ['JBL Charge 5', 2499000, 'Portable Powerbank Speaker'],
    ['Sony SRS-XB13', 799000, 'Extra Bass Portable'],
    ['Logitech Zone Vibe 100', 1499000, 'Office Wireless Headset'],
    ['Razer Kraken V3', 1199000, 'RGB Gaming Headset'],
    ['Samsung Buds FE', 1299000, 'ANC TWS Affordable'],
    ['Marshall Emberton II', 2499000, 'Portable, Classic Sound'],
    ['Audio-Technica ATH-M50x', 1999000, 'Studio Monitor Headphone'],
    ['Xiaomi Redmi Buds 5 Pro', 899000, 'ANC TWS, 38 jam'],
];
foreach ($audio as $i => $p) {
    $produk[] = [$p[0], 'Audio', $p[1], rand(10, 30), $p[2], imgPick('audio', $imgs, $i)];
}

$storage = [
    ['Samsung 990 PRO 1TB', 1899000, 'PCIe 4.0 NVMe, 7450MB/s'],
    ['Kingston NV2 1TB', 899000, 'PCIe 4.0, 3500MB/s'],
    ['Seagate External 2TB', 999000, 'USB 3.0 Portable HDD'],
    ['SanDisk Extreme 128GB', 349000, 'USB 3.2 Flash 400MB/s'],
    ['WD Black SN850X 1TB', 1799000, 'PCIe 4.0 Gaming SSD'],
    ['Samsung T7 Shield 1TB', 1499000, 'Portable SSD IP65'],
    ['Kingston DataTraveler 64GB', 129000, 'USB 3.2 Flash'],
    ['Seagate Barracuda 2TB', 899000, '3.5" Internal HDD'],
    ['Crucial P3 Plus 1TB', 999000, 'PCIe 4.0 NVMe'],
    ['SanDisk Ultra 256GB', 299000, 'MicroSD + Adapter'],
    ['WD My Passport 2TB', 1099000, 'Portable HDD USB-C'],
    ['Samsung 870 EVO 1TB', 1299000, '2.5" SATA SSD'],
    ['Kingston Canvas Select 128GB', 149000, 'MicroSD U1'],
    ['Lexar JumpDrive 128GB', 199000, 'USB 3.2 Flash'],
    ['ADATA XPG 1TB NVMe', 1099000, 'PCIe 3.0/4.0 SSD'],
];
foreach ($storage as $i => $p) {
    $produk[] = [$p[0], 'Storage', $p[1], rand(15, 50), $p[2], imgPick('storage', $imgs, $i)];
}

$aksesoris = [
    ['Logitech MX Master 3S', 1299000, 'Wireless Ergonomic Mouse'],
    ['Keychron K2 Mechanical', 1450000, 'Wireless RGB Hot-swap'],
    ['Logitech C920 HD Pro', 899000, 'Webcam Full HD 1080p'],
    ['Anker 65W GaN Prime', 549000, 'Fast Charger 3 Port'],
    ['Anker Powerbank 20000 30W', 699000, 'PD 30W, LED Display'],
    ['Razer Goliathus Extended', 399000, 'XXL Gaming Mousepad'],
    ['Kabel USB-C Anker 100W 2m', 249000, 'Braided Fast Charge'],
    ['Stand Laptop Aluminium', 329000, 'Ergonomic Foldable'],
    ['Logitech K380 Keyboard', 449000, 'Multi-device Bluetooth'],
    ['Razer DeathAdder V3', 899000, 'Gaming Mouse Lightweight'],
    ['UGREEN USB-C Hub 7in1', 499000, 'HDMI, USB, SD, PD'],
    ['Baseus GaN 100W Charger', 699000, '4 Port Desktop Charger'],
    ['Logitech Pebble Mouse', 299000, 'Silent Wireless Compact'],
    ['Keychron C1 Wired', 799000, 'Mechanical TKL'],
    ['Xiaomi Mi Wireless Mouse', 149000, '2.4G Compact'],
    ['Spigen Case iPhone 15', 299000, 'Clear Protective Case'],
    ['Tempered Glass Universal', 49000, 'Anti Gores 9H'],
    ['Cooling Pad Laptop 6 Fan', 249000, 'RGB Cooling Stand'],
    ['Webcam Nexigo 1080p', 599000, 'Autofocus Stereo Mic'],
    ['Anker USB-C to HDMI 4K', 199000, 'Adapter 4K 60Hz'],
];
foreach ($aksesoris as $i => $p) {
    $produk[] = [$p[0], 'Aksesoris', $p[1], rand(20, 60), $p[2], imgPick('aksesoris', $imgs, $i)];
}

$stmt = mysqli_prepare($conn, "INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi, gambar) VALUES (?,?,?,?,?,?)");
$okProduk = 0;
foreach ($produk as $p) {
    $nama = $p[0]; $kat = $p[1]; $harga = (float)$p[2]; $stok = (int)$p[3]; $desc = $p[4]; $gambar = $p[5];
    mysqli_stmt_bind_param($stmt, "ssdiss", $nama, $kat, $harga, $stok, $desc, $gambar);
    if (mysqli_stmt_execute($stmt)) $okProduk++;
}
mysqli_stmt_close($stmt);
echo "Produk: $okProduk OK<br>";

// ========== PENJUALAN + DETAIL ==========
$adminId = 1;
$metode = ['Tunai', 'Transfer Bank', 'QRIS', 'E-Wallet', 'Kartu Debit'];
$okJual = 0;
$okDetail = 0;

for ($i = 1; $i <= 25; $i++) {
    $hari = $i;
    $produkId = rand(1, min(50, $okProduk));
    $qty = rand(1, 3);

    $q = mysqli_query($conn, "SELECT harga FROM produk WHERE id = $produkId");
    $row = mysqli_fetch_assoc($q);
    if (!$row) continue;
    $harga = (float)$row['harga'];
    $subtotal = $harga * $qty;
    $ket = 'Transaksi retail #' . $i;
    $met = $metode[array_rand($metode)];

    $stmt = mysqli_prepare($conn, "INSERT INTO penjualan (tanggal, total, keterangan, metode_pembayaran, status, user_id) VALUES (DATE_SUB(NOW(), INTERVAL ? DAY), ?, ?, ?, 'lunas', ?)");
    mysqli_stmt_bind_param($stmt, "idssi", $hari, $subtotal, $ket, $met, $adminId);
    if (mysqli_stmt_execute($stmt)) {
        $penjualanId = mysqli_insert_id($conn);
        $okJual++;
        mysqli_stmt_close($stmt);

        $stmt2 = mysqli_prepare($conn, "INSERT INTO detail_penjualan (penjualan_id, produk_id, qty, harga_satuan, subtotal) VALUES (?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt2, "iiidi", $penjualanId, $produkId, $qty, $harga, $subtotal);
        if (mysqli_stmt_execute($stmt2)) $okDetail++;
        mysqli_stmt_close($stmt2);
    } else {
        mysqli_stmt_close($stmt);
    }
}
echo "Penjualan: $okJual OK | Detail: $okDetail OK<br>";

// ========== PENGELUARAN ==========
$pengeluaran = [
    [0, 'Operasional', 'Listrik & air toko bulan ini', 1250000],
    [0, 'Gaji', 'Gaji kasir & staff', 8500000],
    [2, 'Persediaan', 'Restock smartphone & aksesoris', 18500000],
    [5, 'Marketing', 'Iklan Meta Ads + Google Ads', 2750000],
    [7, 'Sewa', 'Sewa ruko bulan berjalan', 7500000],
    [10, 'Operasional', 'Internet & telepon toko', 650000],
    [12, 'Maintenance', 'Service AC & kebersihan', 850000],
    [15, 'Persediaan', 'Pembelian monitor & laptop', 32000000],
    [18, 'Gaji', 'Bonus kinerja karyawan', 2500000],
    [20, 'Marketing', 'Banner & materi promosi', 1200000],
    [3, 'Operasional', 'ATK & perlengkapan toko', 450000],
    [8, 'Persediaan', 'Restock audio & storage', 9800000],
    [14, 'Marketing', 'Sponsorship event lokal', 3500000],
    [22, 'Sewa', 'Biaya parkir & keamanan', 800000],
    [25, 'Operasional', 'Maintenance website & domain', 500000],
];

$stmt = mysqli_prepare($conn, "INSERT INTO pengeluaran (tanggal, kategori, keterangan, jumlah, user_id) VALUES (DATE_SUB(CURDATE(), INTERVAL ? DAY), ?, ?, ?, ?)");
$okKeluar = 0;
foreach ($pengeluaran as $pg) {
    mysqli_stmt_bind_param($stmt, "issdi", $pg[0], $pg[1], $pg[2], $pg[3], $adminId);
    if (mysqli_stmt_execute($stmt)) $okKeluar++;
}
mysqli_stmt_close($stmt);
echo "Pengeluaran: $okKeluar OK<br>";

echo "<hr><b>SELESAI!</b><br>";
echo "Hapus file <code>seed_all.php</code> sekarang.<br>";
echo "Refresh Dashboard / Produk / Karyawan / Riwayat.<br>";
echo "<br>Login admin: <b>admin</b> / <b>admin123</b>";

mysqli_close($conn);
?>
