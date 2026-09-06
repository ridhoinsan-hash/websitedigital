# 📘 DOKUMENTASI LENGKAP
# Digital Electronic Ridho

**Versi:** 2.0 (Updated)  
**Teknologi:** PHP Native • MySQL • HTML5 • CSS3 • JavaScript  
**Tipe Aplikasi:** Sistem Informasi Toko Elektronik Digital (Multi-Role)

---

## 1. RINGKASAN APLIKASI

**Digital Electronic Ridho** adalah aplikasi web toko elektronik yang mendukung multi-role (Admin, Kasir, Staff, Pembeli). Fitur utama meliputi:

- Manajemen produk (CRUD) dengan gambar
- 50 produk elektronik digital siap pakai
- Checkout modern dengan metode pembayaran + upload bukti
- Halaman sukses pembayaran dengan centang hijau animasi
- CRUD karyawan / user
- Pencatatan penjualan & pengeluaran
- Dashboard statistik (pemasukan, pengeluaran, laba)
- Login modern bergaya landing page + konfirmasi logout Ya/Tidak

---

## 2. STRUKTUR FOLDER

```
DigitalElectronicRidho/
│
├── includes/
│   ├── config.php          → Koneksi database + helper formatRupiah()
│   ├── auth.php            → Autentikasi, session, role helper
│   └── navbar.php          → Navigasi dinamis sesuai role
│
├── css/
│   └── style.css           → Stylesheet utama (tema biru-ungu)
│
├── js/
│   └── script.js           → Animasi, konfirmasi hapus, validasi
│
├── uploads/                → Folder upload bukti pembayaran & gambar
│   └── .gitkeep
│
├── login.php               → Halaman login + ilustrasi toko modern
├── logout.php              → Konfirmasi logout (Ya / Tidak)
├── index.php               → Dashboard (Admin, Kasir, Staff)
│
├── produk.php              → Daftar / katalog produk
├── tambah_produk.php       → Tambah produk (Admin)
├── edit_produk.php         → Edit produk (Admin)
├── hapus_produk.php        → Hapus produk (Admin)
│
├── penjualan.php           → Riwayat penjualan
├── tambah_penjualan.php    → Checkout modern (semua role)
├── detail_penjualan.php    → Detail transaksi + bukti bayar
├── hapus_penjualan.php     → Hapus transaksi
├── sukses_pembayaran.php   → Halaman "Pembayaran Berhasil" ✅
│
├── pengeluaran.php         → Daftar pengeluaran (Admin)
├── tambah_pengeluaran.php  → Tambah pengeluaran
├── hapus_pengeluaran.php   → Hapus pengeluaran
│
├── karyawan.php            → Daftar karyawan (Admin)
├── tambah_karyawan.php     → Tambah karyawan
├── edit_karyawan.php       → Edit karyawan
├── hapus_karyawan.php      → Hapus karyawan
│
├── database.sql            → Skema + data awal (50 produk)
├── README.md               → Panduan singkat
└── DOKUMENTASI.md          → Dokumentasi lengkap (file ini)
```

---

## 3. CARA INSTALASI & MENJALANKAN

### Persyaratan
- XAMPP / Laragon / WAMP (Apache + MySQL + PHP 7.4+)
- Browser modern (Chrome, Firefox, Edge)

### Langkah Instalasi

1. **Extract** file ZIP aplikasi.
2. **Copy** folder `DigitalElectronicRidho` ke:
   - XAMPP → `C:\xampp\htdocs\`
   - Laragon → `C:\laragon\www\`
3. **Start** Apache dan MySQL di XAMPP/Laragon.
4. Buka **phpMyAdmin** → `http://localhost/phpmyadmin`
5. Klik **Import** → pilih file `database.sql` → klik **Go**.
   > ⚠️ **Wajib re-import** jika sebelumnya sudah pernah import versi lama, agar 50 produk + kolom pembayaran + karyawan masuk.
6. Buka browser:
   ```
   http://localhost/DigitalElectronicRidho/
   ```
7. Login menggunakan akun demo di bawah.

### Konfigurasi Database (jika perlu)

Edit file `includes/config.php`:

```php
$host     = "localhost";
$user     = "root";
$password = "";              // Kosongkan jika XAMPP default
$database = "db_digital_ridho";
```

---

## 4. AKUN DEMO

| Username  | Password    | Role          | Hak Akses |
|-----------|-------------|---------------|-----------|
| `admin`   | `admin123`  | Administrator | Full akses: Dashboard, Produk, Penjualan, Pengeluaran, Karyawan |
| `kasir`   | `kasir123`  | Kasir         | Dashboard, Produk (lihat), Penjualan / Checkout |
| `kasir2`  | `kasir123`  | Kasir         | Sama seperti kasir |
| `staff1`  | `pembeli123`| Staff         | Dashboard, lihat produk |
| `staff2`  | `pembeli123`| Staff         | Dashboard, lihat produk |
| `pembeli` | `pembeli123`| Pembeli       | Katalog produk + Checkout sendiri |

> Password di-hash menggunakan `password_hash()` (bcrypt).

---

## 5. FITUR PER ROLE

### 👑 Administrator
- Dashboard lengkap (produk, stok, pemasukan, pengeluaran, laba)
- CRUD Produk (tambah, edit, hapus + gambar)
- Penjualan / Checkout
- Pengeluaran (CRUD)
- **CRUD Karyawan** (tambah, edit, hapus, nonaktifkan)
- Lihat detail transaksi + bukti pembayaran

### 🧾 Kasir
- Dashboard
- Lihat produk
- Proses penjualan / checkout
- Lihat riwayat & detail transaksi

### 👷 Staff
- Dashboard
- Lihat katalog produk

### 🛒 Pembeli
- Lihat katalog produk (dengan gambar)
- Checkout sendiri
- Upload bukti pembayaran
- Halaman sukses pembayaran
- Lihat detail transaksi sendiri

---

## 6. ALUR CHECKOUT & PEMBAYARAN

### Langkah Checkout (`tambah_penjualan.php`)

1. **Pilih Produk**
   - Dropdown produk stok > 0
   - Preview gambar + nama + harga muncul otomatis
   - Input jumlah (qty)

2. **Pilih Metode Pembayaran**
   - 💵 Tunai
   - 🏦 Transfer Bank
   - 📱 QRIS
   - 💳 E-Wallet
   - 💳 Kartu Debit/Kredit

3. **Upload Bukti Pembayaran**
   - **Wajib** jika metode ≠ Tunai
   - Format: JPG, JPEG, PNG, WEBP, PDF
   - Maksimal 3 MB
   - Drag & drop didukung
   - File disimpan di folder `uploads/`

4. **Proses Pembayaran**
   - Transaksi disimpan
   - Stok produk dikurangi otomatis
   - Redirect ke halaman sukses

### Halaman Sukses (`sukses_pembayaran.php`)

- Animasi centang hijau besar ✅
- Badge status **Lunas**
- No. transaksi, tanggal, metode, total
- Daftar item yang dibeli
- Link lihat bukti pembayaran (jika ada)
- Tombol: Detail Transaksi / Belanja Lagi / Daftar Penjualan

---

## 7. DATABASE

**Nama Database:** `db_digital_ridho`

### Tabel `users`
| Kolom          | Tipe              | Keterangan                    |
|----------------|-------------------|-------------------------------|
| id             | INT PK AI         | ID user                       |
| username       | VARCHAR(50) UNIQUE| Username login                |
| password       | VARCHAR(255)      | Hash bcrypt                   |
| nama_lengkap   | VARCHAR(100)      | Nama lengkap                  |
| email          | VARCHAR(100)      | Email (opsional)              |
| no_hp          | VARCHAR(20)       | No. HP (opsional)             |
| role           | ENUM              | admin / kasir / staff / pembeli |
| status         | ENUM              | aktif / nonaktif              |
| created_at     | TIMESTAMP         | Waktu dibuat                  |

### Tabel `produk`
| Kolom        | Tipe           | Keterangan              |
|--------------|----------------|-------------------------|
| id           | INT PK AI      | ID produk               |
| nama_produk  | VARCHAR(120)   | Nama produk             |
| kategori     | VARCHAR(50)    | Laptop, Smartphone, dll |
| harga        | DECIMAL(12,2)  | Harga jual              |
| stok         | INT            | Jumlah stok             |
| deskripsi    | TEXT           | Deskripsi produk        |
| gambar       | VARCHAR(255)   | URL / path gambar       |
| created_at   | TIMESTAMP      | Waktu dibuat            |

**Kategori produk (50 item):**
- Laptop (10)
- Smartphone (12)
- Tablet (5)
- Monitor (5)
- Audio (6)
- Storage & Aksesoris (12)

### Tabel `penjualan`
| Kolom              | Tipe           | Keterangan                          |
|--------------------|----------------|-------------------------------------|
| id                 | INT PK AI      | ID transaksi                        |
| tanggal            | DATETIME       | Waktu transaksi                     |
| total              | DECIMAL(14,2)  | Total bayar                         |
| keterangan         | VARCHAR(255)   | Catatan opsional                    |
| metode_pembayaran  | VARCHAR(50)    | Tunai / Transfer / QRIS / dll       |
| bukti_pembayaran   | VARCHAR(255)   | Path file bukti                     |
| status             | ENUM           | pending / lunas / batal             |
| user_id            | INT FK         | User yang melakukan transaksi       |
| created_at         | TIMESTAMP      | Waktu dibuat                        |

### Tabel `detail_penjualan`
| Kolom         | Tipe           | Keterangan     |
|---------------|----------------|----------------|
| id            | INT PK AI      | ID detail      |
| penjualan_id  | INT FK         | Relasi header  |
| produk_id     | INT FK         | Produk dibeli  |
| qty           | INT            | Jumlah         |
| harga_satuan  | DECIMAL(12,2)  | Harga saat itu |
| subtotal      | DECIMAL(14,2)  | qty × harga    |

### Tabel `pengeluaran`
| Kolom       | Tipe           | Keterangan              |
|-------------|----------------|-------------------------|
| id          | INT PK AI      | ID pengeluaran          |
| tanggal     | DATE           | Tanggal                 |
| kategori    | VARCHAR(50)    | Gaji, Sewa, Marketing…  |
| keterangan  | VARCHAR(255)   | Keterangan              |
| jumlah      | DECIMAL(14,2)  | Nominal                 |
| user_id     | INT FK         | User yang mencatat      |
| created_at  | TIMESTAMP      | Waktu dibuat            |

---

## 8. HALAMAN LOGIN

- Layout **split screen** modern (mirip Jubelio)
- Kiri: ilustrasi toko + kartu UI mengambang (produk, statistik, badge)
- Kanan: form login bersih
- Cek status akun (nonaktif tidak bisa login)
- Redirect otomatis sesuai role setelah login

---

## 9. KONFIRMASI LOGOUT

Saat klik **Logout**:
1. Muncul halaman konfirmasi
2. Tombol **Ya, Logout** → session dihapus → kembali ke login
3. Tombol **Tidak** → kembali ke dashboard / katalog

---

## 10. FITUR CRUD KARYAWAN

Hanya **Admin** yang bisa mengakses menu **Karyawan**.

| Aksi   | File                  | Keterangan                                      |
|--------|-----------------------|-------------------------------------------------|
| List   | `karyawan.php`        | Tabel semua user + role + status                |
| Tambah | `tambah_karyawan.php` | Username, password, nama, email, HP, role, status |
| Edit   | `edit_karyawan.php`   | Ubah data + ganti password (opsional)           |
| Hapus  | `hapus_karyawan.php`  | Tidak bisa hapus akun sendiri                   |

Role yang bisa dipilih: `admin`, `kasir`, `staff`, `pembeli`.

---

## 11. DASHBOARD

Menampilkan statistik real-time:
- Total produk
- Total stok
- Stok rendah (< 10)
- Jumlah transaksi
- Total pemasukan (dari penjualan)
- Total pengeluaran
- Laba / Rugi (pemasukan − pengeluaran)
- 5 transaksi terbaru
- 5 pengeluaran terbaru

---

## 12. KEAMANAN

- Password di-hash dengan `password_hash()` / `password_verify()`
- Prepared statement (mysqli) untuk mencegah SQL Injection
- Session-based authentication
- Role-based access control (`requireRole()`, `hasRole()`)
- Validasi upload file (ekstensi + ukuran)
- Escape output dengan `htmlspecialchars()`
- Tidak bisa menghapus akun sendiri

---

## 13. CATATAN PENTING

1. **Import ulang `database.sql`** setiap kali ada update besar agar struktur tabel dan data sample sesuai.
2. Folder `uploads/` harus **writable** (permission 755 atau 775) agar upload bukti pembayaran berhasil.
3. Gambar produk default menggunakan URL Unsplash (butuh internet). Bisa diganti path lokal.
4. Untuk production: ganti password default, aktifkan HTTPS, dan batasi akses folder `uploads/`.

---

## 14. TROUBLESHOOTING

| Masalah | Solusi |
|---------|--------|
| Koneksi gagal | Cek `includes/config.php` (host, user, password, nama DB) |
| Halaman blank | Aktifkan `display_errors` di PHP atau cek log error Apache |
| Upload gagai | Pastikan folder `uploads/` ada dan writable |
| Login gagal setelah import | Pastikan `database.sql` ter-import penuh (cek tabel `users`) |
| Stok tidak berkurang | Pastikan transaksi commit berhasil (cek error di form checkout) |
| Role tidak sesuai | Logout lalu login ulang, atau cek kolom `role` di tabel `users` |

---

## 15. RINGKASAN FILE PENTING

| File | Fungsi |
|------|--------|
| `database.sql` | Skema + 50 produk + sample transaksi & pengeluaran |
| `login.php` | Login + ilustrasi modern |
| `logout.php` | Konfirmasi Ya/Tidak |
| `tambah_penjualan.php` | Checkout + metode bayar + upload bukti |
| `sukses_pembayaran.php` | Halaman sukses centang hijau |
| `karyawan.php` + related | CRUD karyawan |
| `includes/auth.php` | Sistem role & session |

---

**Digital Electronic Ridho**  
Aplikasi toko elektronik digital siap pakai.  
Dokumentasi ini mencakup seluruh fitur versi terbaru.



---

## 16. SISTEM DAFTAR AKUN & CAPTCHA

### Registrasi Publik (`register.php`)
- Siapa pun dapat mendaftar sebagai **pembeli**
- Field: nama lengkap, username, password, konfirmasi password, email, no. HP
- Validasi: username unik, email unik (jika diisi), password min 6 karakter
- Role otomatis = `pembeli`, status = `aktif`
- Setelah sukses → link ke halaman login

### CAPTCHA
- File: `captcha_image.php` + `includes/captcha.php`
- Kode acak 5 karakter (huruf/angka mudah dibaca)
- Gambar PNG (GD) atau SVG fallback
- Dipakai di **Login** dan **Daftar**
- Satu kali pakai, kedaluwarsa 10 menit
- Klik gambar / tombol 🔄 untuk refresh

### Profil (`profil.php`)
- User login dapat mengubah nama, email, no. HP
- Ganti password opsional
- Username & role tidak dapat diubah sendiri

### Alur Auth Lengkap
1. Pengunjung → `register.php` (CAPTCHA) → akun pembeli
2. Login → `login.php` (CAPTCHA) → redirect sesuai role
3. Profil → `profil.php`
4. Logout → konfirmasi Ya/Tidak

