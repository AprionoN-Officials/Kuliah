# Panduan Login Admin dan User

## Update Password Admin
Karena password admin di database masih plain text ('admin'), Anda perlu mengupdate dengan hash yang benar.

**Cara 1: Melalui phpMyAdmin**
1. Buka phpMyAdmin
2. Pilih database `datagame`
3. Klik tab SQL
4. Jalankan query:
```sql
UPDATE users SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';
```
5. Sekarang admin bisa login dengan:
   - Username: `admin`
   - Password: `password`

**Cara 2: Buat Admin Baru via Register**
1. Akses halaman register
2. Buat akun baru
3. Login ke phpMyAdmin
4. Update role user tersebut menjadi 'admin':
```sql
UPDATE users SET role = 'admin' WHERE username = 'username_baru';
```

---

## Cara Login

### Login sebagai Admin
- **URL**: http://localhost/game-admin/login.php
- **Username**: admin
- **Password**: password (setelah update hash)
- **Redirect**: Otomatis ke `admin_dashboard.php`

### Login sebagai User
- **URL**: http://localhost/game-admin/login.php
- **Username**: user (atau akun yang dibuat via register)
- **Password**: (password yang didaftarkan)
- **Redirect**: Otomatis ke `index.php`

---

## Fitur Admin
Setelah login sebagai admin, Anda akan melihat dashboard dengan menu:
1. **Dashboard** - Statistik sistem
2. **Manajemen Game** - Tambah, Edit, Hapus game
3. **Manajemen User** - Kelola user, reset password, edit role
4. **Manajemen Saldo** - Isi saldo user

## Fitur User
Setelah login sebagai user, Anda akan melihat dashboard dengan menu:
1. **Dashboard** - Lihat daftar game
2. **Akun Saya** - Profil user
3. **Library** - Game yang telah disewa/dibeli
4. **Daftar Game** - Semua game tersedia
5. **Isi Saldo** - Top up saldo

---

## Proteksi Sistem
- Admin tidak bisa mengakses halaman user (otomatis redirect ke admin dashboard)
- User tidak bisa mengakses halaman admin (otomatis redirect ke login)
- Semua halaman dilindungi dengan session check
