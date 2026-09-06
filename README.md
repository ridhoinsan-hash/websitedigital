# ⚡ Digital Electronic Ridho (Updated)

Aplikasi toko elektronik modern dengan **multi-role**, CRUD karyawan, 50 produk, checkout, gambar produk, dan keuangan.

Teknologi: **PHP • MySQL • HTML • CSS • JavaScript**

---

## 🔐 Akun Default

| Username  | Password    | Role          | Akses |
|-----------|-------------|---------------|-------|
| `admin`   | `admin123`  | Administrator | Full (produk, penjualan, pengeluaran, karyawan, dashboard) |
| `kasir`   | `kasir123`  | Kasir         | Dashboard, produk, penjualan/checkout |
| `kasir2`  | `kasir123`  | Kasir         | Dashboard, produk, penjualan/checkout |
| `staff1`  | `pembeli123`| Staff         | Dashboard, lihat produk |
| `pembeli` | `pembeli123`| Pembeli       | Katalog produk + Checkout |

---

## ✨ Fitur Baru

- **CRUD Karyawan** (admin only) — tambah, edit, hapus akun admin/kasir/staff/pembeli
- **50 Produk** digital elektronik (Laptop, Smartphone, Tablet, Monitor, Audio, Storage, Aksesoris)
- **Data Pemasukan & Pengeluaran** diperbarui (sample transaksi & biaya operasional)
- **Login** dengan ilustrasi toko modern (desain Grok)
- **Logout** wajib konfirmasi **Ya / Tidak**
- **Daftar Akun** publik (role pembeli) + **CAPTCHA**
- **Login** dilindungi CAPTCHA
- **Profil Saya** — ubah nama, email, HP, password

---

## 📁 Struktur Folder

```
DigitalElectronicRidho/
├── includes/
│   ├── config.php
│   ├── auth.php
│   └── navbar.php
├── css/style.css
├── js/script.js
├── uploads/
├── login.php              ← login + CAPTCHA + ilustrasi
├── register.php           ← daftar akun + CAPTCHA
├── captcha_image.php      ← generate gambar CAPTCHA
├── profil.php             ← profil user login
├── logout.php             ← konfirmasi Ya/Tidak
├── index.php              → Dashboard
├── produk.php
├── tambah_produk.php / edit_produk.php / hapus_produk.php
├── penjualan.php / tambah_penjualan.php / detail_penjualan.php / hapus_penjualan.php
├── pengeluaran.php / tambah_pengeluaran.php / hapus_pengeluaran.php
├── karyawan.php           → Daftar karyawan
├── tambah_karyawan.php
├── edit_karyawan.php
├── hapus_karyawan.php
├── database.sql
└── README.md
```

---

## 🚀 Cara Menjalankan

1. **Install XAMPP** → Start Apache + MySQL
2. Copy folder `DigitalElectronicRidho` ke `C:\xampp\htdocs\`
3. Buka **phpMyAdmin** → Import file **`database.sql`** (penting: re-import agar 50 produk & karyawan masuk)
4. Buka browser:
   ```
   http://localhost/DigitalElectronicRidho/
   ```
5. Login sesuai role di atas

---

## 🗄️ Database

- `users` — akun + role (admin / kasir / staff / pembeli) + status
- `produk` — 50 item inventaris + gambar
- `penjualan` + `detail_penjualan`
- `pengeluaran`
