# PRD / Document Plan: Setup Login Authentication Admin

**Role:** Technical Architect / PM / Senior Fullstack Developer  
**Scope:** Mengaktifkan autentikasi admin menggunakan Laravel Breeze agar seluruh resource, modul, fitur, dan halaman aplikasi hanya bisa diakses oleh petugas/admin yang sah.

---

## 1. Tujuan

Membangun alur login internal perusahaan menggunakan Laravel Breeze yang sudah tersedia di project. Autentikasi ini harus melindungi seluruh halaman admin dan modul aplikasi dari akses user yang belum login.

Fitur ini berfokus pada login, proses verifikasi user terdaftar, redirect setelah login, logout, dan proteksi route menggunakan middleware bawaan Breeze/Laravel.

## 2. Ruang Lingkup

Implementasi dilakukan menggunakan pola MVC dan sumber daya Breeze yang sudah tersedia:

- **View login:** `resources/views/auth/login.blade.php`
- **Controller login:** `App\Http\Controllers\Auth\AuthenticatedSessionController`
- **Request autentikasi:** request login Breeze yang sudah tersedia
- **Model user:** `App\Models\User`
- **Routes auth:** `routes/auth.php`
- **Routes aplikasi:** `routes/web.php`

Scope pekerjaan:

- Menyesuaikan halaman login
- Menyesuaikan proses login agar memakai username dan password
- Menonaktifkan akses registrasi dari publik
- Mengarahkan user berhasil login ke dashboard admin
- Melindungi semua route aplikasi menggunakan middleware auth

## 3. Batasan Scope

Jangan mengerjakan fitur di luar kebutuhan autentikasi dasar.

Tidak termasuk dalam scope:

- Modul registrasi user publik
- Manajemen user/admin
- Role dan permission multi-level
- Reset password jika tidak dibutuhkan oleh flow internal
- Email verification
- Social login
- Audit log login
- Perubahan besar pada struktur database tanpa konfirmasi

Jika ternyata kolom `username` belum tersedia di tabel `users`, implementer perlu mengonfirmasi apakah boleh menambahkan kolom tersebut melalui migration. Jangan mengganti requirement menjadi email login tanpa konfirmasi, karena dokumen ini meminta field username.

## 4. Kondisi Saat Ini

Laravel Breeze sudah tersedia di project, termasuk:

- View login default Breeze
- Controller session login
- Route auth
- Model `User`

Namun kondisi default Breeze masih perlu disesuaikan karena:

- Login default Breeze umumnya memakai email, sementara requirement meminta username
- Route register masih tersedia pada konfigurasi Breeze default
- Redirect setelah login harus diarahkan ke dashboard admin
- Route admin belum seluruhnya dipastikan berada di dalam middleware `auth`
- Tampilan login masih perlu disesuaikan dengan gaya visual admin dashboard

## 5. Kebutuhan Login Page

Halaman login berada di:

- `resources/views/auth/login.blade.php`

Kebutuhan UI:

- Tampilkan hanya field `username` dan `password`
- Tampilkan tombol submit login
- Tambahkan icon mata pada field password untuk toggle show/hide password
- Gunakan Tailwind CSS yang sudah tersedia
- Desain minimalis, clean, modern, dan konsisten dengan nuansa dashboard admin
- Hindari link registrasi publik
- Hindari elemen yang tidak relevan untuk aplikasi internal

Kebutuhan pesan error:

- Jika login gagal, tampilkan pesan sederhana:
  - `Login gagal. Username/Password salah`
- Pesan error harus mudah terlihat, singkat, dan tidak membocorkan detail apakah username atau password yang salah

## 6. Kebutuhan Proses Autentikasi

Gunakan flow Breeze yang sudah ada sebagai dasar.

Aturan autentikasi:

- Login menggunakan `username` dan `password`
- Validasi input dilakukan di sisi server
- User hanya boleh masuk jika data login cocok dengan user terdaftar
- Setelah berhasil login, session diregenerasi sesuai standar Breeze/Laravel
- Setelah logout, session dibersihkan dan user diarahkan keluar dari area admin

Model yang digunakan:

- Gunakan `App\Models\User` sebagai representasi user yang boleh login

Catatan teknis:

- Jika project saat ini masih memakai `email` sebagai identifier login, ubah flow login agar menggunakan `username`
- Jika database belum memiliki kolom `username`, butuh keputusan eksplisit sebelum menambahkan migration

## 7. Redirect Setelah Login

Jika login berhasil:

- Arahkan user ke halaman dashboard admin
- Target halaman: `/admin/dashboard`
- Route target yang diharapkan: `admin.dashboard`

Jika user mencoba membuka protected route tanpa login:

- Redirect ke halaman login
- Setelah login berhasil, user dapat diarahkan kembali ke intended URL jika masih berada dalam scope admin yang valid

## 8. Proteksi Route / Middleware

Semua modul, fitur, dan halaman aplikasi harus dilindungi menggunakan middleware auth bawaan Breeze/Laravel.

Route yang perlu diproteksi:

- Dashboard admin
- Category management
- Transaction management
- Product management jika masih tersedia
- Invoice management
- Customer endpoint yang dipakai dari invoice
- Endpoint Ajax internal seperti chart dashboard
- Semua route admin lain yang merupakan bagian aplikasi internal

Arahan implementasi:

- Kelompokkan route admin di `routes/web.php` ke dalam middleware `auth`
- Pastikan endpoint Ajax admin juga berada di bawah middleware yang sama
- Route login tetap berada di luar middleware auth dan memakai middleware guest
- Logout tetap memakai middleware auth

## 9. Registrasi

Karena aplikasi ini adalah aplikasi internal perusahaan, fitur registrasi publik tidak diperlukan.

Aturan:

- Jangan tampilkan link registrasi pada halaman login
- Nonaktifkan route register publik dari Breeze
- Jika route register masih dibutuhkan untuk kebutuhan seeding/admin internal, jangan ekspos sebagai halaman publik tanpa konfirmasi tambahan

## 10. Alur User

Alur login normal:

1. User membuka halaman login
2. User mengisi username dan password
3. Sistem memvalidasi credential
4. Jika gagal, tampilkan pesan error sederhana
5. Jika berhasil, sistem membuat session login
6. User diarahkan ke dashboard admin

Alur akses protected route:

1. User membuka halaman admin tanpa login
2. Middleware auth menolak akses
3. User diarahkan ke halaman login
4. Setelah login berhasil, user masuk ke area admin sesuai aturan redirect

## 11. Acceptance Criteria

- Halaman login hanya menampilkan field username dan password
- Field password memiliki icon mata untuk show/hide password
- Tampilan login memakai Tailwind CSS dan konsisten dengan nuansa admin dashboard
- Login gagal menampilkan pesan `Login gagal. Username/Password salah`
- Login berhasil mengarahkan user ke `/admin/dashboard`
- Route register publik tidak lagi tersedia untuk user umum
- Semua route admin dilindungi middleware auth
- User yang belum login tidak bisa mengakses dashboard, modul, fitur, halaman, atau endpoint Ajax admin
- User yang sudah login dapat mengakses dashboard dan modul admin sesuai route yang tersedia
- Logout membersihkan session dan mengeluarkan user dari area admin

## 12. Catatan untuk Implementer

Prioritaskan penggunaan resource Breeze yang sudah tersedia. Jangan membuat sistem autentikasi baru dari nol jika Breeze sudah menyediakan flow yang diperlukan.

Jaga scope tetap sempit: login internal, proteksi route, redirect dashboard, dan penyederhanaan halaman login. Jika ditemukan kebutuhan schema baru untuk `username`, minta konfirmasi sebelum memperluas pekerjaan ke migration database.
