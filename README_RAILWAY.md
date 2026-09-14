# Digital Electronic — Railway Ready

Versi ini mempertahankan tampilan Dashboard Digital Electronic dan fitur stok, kasir, penjualan, pengeluaran, karyawan, pengaturan, profil, dan portofolio.

## 1. Upload ke GitHub
PENTING: isi folder ini harus berada LANGSUNG di root repository GitHub.
Jangan sampai menjadi:
`repository/DigitalElectronic/index.php`

Yang benar:
`repository/index.php`
`repository/includes/auth.php`
`repository/css/style.css`
`repository/database.sql`
`repository/Dockerfile`

## 2. Deploy ke Railway
- Buat project baru di Railway.
- Deploy service dari repository GitHub ini.
- Railway akan memakai Dockerfile.
- Tunggu sampai service Online.

## 3. Tambahkan MySQL
Tambahkan service MySQL di project Railway yang sama.

## 4. Hubungkan variable MySQL
Di service website, buka Variables dan pastikan variable dari MySQL tersedia:
- MYSQLHOST
- MYSQLPORT
- MYSQLUSER
- MYSQLPASSWORD
- MYSQLDATABASE

`includes/config.php` sudah membaca variable-variable tersebut otomatis.

## 5. Isi database
Gunakan file `railway_database.sql` untuk database Railway.
File ini sengaja tidak memakai `CREATE DATABASE` atau `USE`, sehingga cocok untuk database yang sudah dibuat Railway.

Import isi `railway_database.sql` ke database MySQL Railway menggunakan client SQL yang kamu gunakan.

## 6. Login demo
- admin / admin123
- kasir / kasir123

## 7. Fitur stok
- Stok tampil di kartu Produk.
- Stok tampil di dashboard.
- Admin dapat menambah/edit stok.
- Produk stok 0 tidak bisa dibeli.
- Saat transaksi berhasil, stok otomatis berkurang.
- Saat transaksi dihapus, stok otomatis dikembalikan.

## Catatan
Upload bukti pembayaran tersimpan di folder `uploads`. Pada hosting container seperti Railway, filesystem aplikasi dapat bersifat ephemeral. Untuk penyimpanan file jangka panjang, gunakan object storage bila nanti dibutuhkan.
