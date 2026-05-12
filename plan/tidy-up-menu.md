# PRD / Document Plan: Tidy Up Sidebar Menu

**Role:** Technical Architect / PM / Senior Fullstack Developer  
**Scope:** Merapihkan menu sidebar admin agar hanya menampilkan menu yang benar-benar relevan, dapat diklik, dan memiliki indikator aktif yang akurat.

---

## 1. Tujuan

Merapihkan struktur menu pada sidebar admin agar pengalaman navigasi menjadi jelas, konsisten, dan tidak membingungkan user. Sidebar harus menghapus item placeholder atau menu mati, lalu hanya menampilkan menu yang memang sudah tersedia atau memang diputuskan sebagai bagian dari scope aktif.

Fokus pekerjaan ini hanya pada perapihan navigasi sidebar, bukan pada pembangunan fitur bisnis baru.

## 2. Ruang Lingkup

Pekerjaan dilakukan pada komponen sidebar yang sudah ada:

- **View:** `resources/views/components/admin/sidebar.blade.php`

Dokumen ini hanya mengatur:

- Penyederhanaan daftar menu
- Aktivasi link untuk menu yang memang tersedia
- Penandaan active state pada menu yang sedang dibuka
- Penghapusan menu placeholder yang belum memiliki fitur atau tujuan jelas

Dokumen ini tidak mencakup pembuatan modul bisnis baru, pembuatan CRUD baru, atau restrukturisasi layout admin di luar sidebar.

## 3. Temuan Kondisi Saat Ini

Kondisi sidebar saat ini belum rapi karena:

- Masih ada beberapa menu yang memakai `href="#"` dan tidak bisa diklik
- Ada submenu yang sifatnya placeholder dan tidak terhubung ke fitur nyata
- Struktur menu belum mencerminkan fitur yang benar-benar tersedia di project
- Indikator active state belum konsisten untuk seluruh menu yang relevan

Dari audit route dan halaman yang tersedia saat ini:

- **Sudah tersedia sebagai halaman/fitur:** Dashboard, Master Kategori, daftar Invoice, form Buat Invoice
- **Belum ditemukan sebagai halaman master dedicated:** Master Pelanggan / Customer

Catatan penting:

- Saat ini customer baru terindikasi tersedia sebagai bagian dari flow invoice, khususnya endpoint pembuatan customer dari form invoice
- Jika menu **Master Pelanggan/Customer** tetap diwajibkan tampil sebagai menu sidebar yang bisa dibuka ke halaman sendiri, maka itu membutuhkan konfirmasi tambahan karena halaman/route dedicated-nya belum terlihat tersedia pada scope saat ini

## 4. Hasil Sidebar yang Diharapkan

Sidebar harus dirapihkan menjadi lebih pendek, langsung, dan hanya memuat menu yang berguna.

Menu target minimum:

- **Dashboard**
- **Master Kategori Transaksi**
- **Buat Invoice**

Menu opsional bersyarat:

- **Master Pelanggan / Customer**

Aturan untuk menu customer:

- Jika memang sudah ada halaman dan route customer dedicated, aktifkan sebagai menu normal
- Jika belum ada halaman dan route customer dedicated, jangan tampilkan sebagai menu aktif palsu
- Jangan mengarahkan user ke `#` atau placeholder hanya demi memenuhi daftar menu

## 5. Prinsip Struktur Navigasi

Struktur sidebar harus mengikuti prinsip berikut:

- Hanya tampilkan menu yang bisa dipakai user
- Hapus menu placeholder seperti ringkasan, budget, preferensi, log aktivitas, atau submenu lain yang belum memiliki implementasi
- Hindari grouping berlebihan jika jumlah menu aktif sedikit
- Jika submenu tidak lagi diperlukan, ubah menjadi menu langsung yang sederhana
- Pertahankan visual language sidebar yang sudah dipakai pada layout admin saat ini

## 6. Aturan Aktivasi Menu

Setiap menu yang tampil harus memiliki tujuan navigasi yang jelas.

Kebutuhan aktivasi:

- **Dashboard** mengarah ke route dashboard admin
- **Master Kategori Transaksi** mengarah ke route kategori admin
- **Buat Invoice** mengarah ke route form pembuatan invoice
- **Master Pelanggan / Customer** hanya boleh diaktifkan jika route halaman customer dedicated benar-benar tersedia

Jika dalam implementasi ditemukan kebutuhan route baru hanya untuk memenuhi menu customer, jangan kerjakan otomatis di task ini tanpa konfirmasi tambahan.

## 7. Active State

Sidebar wajib memiliki indikator aktif untuk halaman yang sedang dibuka.

Aturan active state:

- Menu Dashboard aktif saat user berada di halaman dashboard
- Menu Master Kategori aktif saat user berada di halaman kategori
- Menu Buat Invoice aktif saat user berada di halaman create invoice
- Jika nanti menu Customer tersedia, active state mengikuti route customer

Tujuan active state:

- Memberi orientasi lokasi halaman yang jelas bagi user
- Menghindari kebingungan saat berpindah antarhalaman admin

## 8. Arah Implementasi High Level

Implementasi sebaiknya dilakukan dengan pendekatan berikut:

- Audit seluruh item menu pada sidebar
- Pertahankan hanya item yang memiliki route nyata dan relevan terhadap scope aplikasi saat ini
- Gunakan helper route atau pola `request()->routeIs(...)` yang konsisten untuk active state
- Pastikan label menu mudah dipahami dan sesuai nama fitur
- Jika jumlah menu sedikit, prioritaskan struktur datar daripada nested submenu

## 9. Batasan Implementasi

- Jangan membangun modul customer baru di task ini
- Jangan menambahkan route dummy hanya untuk membuat menu terlihat aktif
- Jangan mengubah layout admin di luar kebutuhan sidebar
- Jangan menambahkan menu yang belum memiliki scope bisnis jelas
- Jangan mempertahankan placeholder `href="#"` pada menu final

## 10. Acceptance Criteria

- Sidebar tidak lagi menampilkan menu mati atau placeholder yang tidak bisa diklik
- Sidebar hanya menampilkan menu yang sesuai dengan fitur yang memang aktif
- Menu Dashboard dapat diklik dan memiliki active state yang benar
- Menu Master Kategori Transaksi dapat diklik dan memiliki active state yang benar
- Menu Buat Invoice dapat diklik dan memiliki active state yang benar
- Menu yang belum memiliki fitur nyata dihapus dari sidebar
- Tidak ada lagi link `#` untuk menu final yang tampil ke user
- Struktur sidebar lebih ringkas dan lebih mudah dipahami

## 11. Dependency / Open Issue

Ada dependency yang perlu diselesaikan sebelum requirement menu customer dianggap final:

- Saat ini belum ditemukan halaman master customer dedicated pada route/view admin
- Jika bisnis memang membutuhkan menu **Master Pelanggan / Customer** sebagai halaman tersendiri, maka perlu konfirmasi apakah:
  1. menu itu sementara disembunyikan sampai modul customer tersedia, atau
  2. perlu dibuat PRD terpisah untuk membangun halaman master customer terlebih dahulu

Sebelum dependency itu jelas, implementasi tidy-up sidebar sebaiknya tidak memaksa menu customer tampil sebagai link aktif.
