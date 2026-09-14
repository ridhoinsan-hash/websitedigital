CREATE TABLE IF NOT EXISTS pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kunci VARCHAR(50) NOT NULL UNIQUE,
    nilai TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO pengaturan (kunci, nilai) VALUES
('nama_toko', 'Digital Electronic'),
('tipe_toko', 'Toko Elektronik'),
('alamat', 'Indonesia'),
('telepon', '0812-0000-0000'),
('footer_struk', 'Terima kasih telah berbelanja di Digital Electronic')
ON DUPLICATE KEY UPDATE nilai = VALUES(nilai);

ALTER TABLE penjualan ADD COLUMN IF NOT EXISTS diskon DECIMAL(14,2) NOT NULL DEFAULT 0;
ALTER TABLE penjualan ADD COLUMN IF NOT EXISTS bayar DECIMAL(14,2) NOT NULL DEFAULT 0;
ALTER TABLE penjualan ADD COLUMN IF NOT EXISTS kembalian DECIMAL(14,2) NOT NULL DEFAULT 0;
ALTER TABLE penjualan ADD COLUMN IF NOT EXISTS nama_pelanggan VARCHAR(100) DEFAULT NULL;
