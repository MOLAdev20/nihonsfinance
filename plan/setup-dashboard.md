# PRD / Document Plan: Dashboard Summary Keuangan Bulanan

**Role:** Technical Architect / PM / Senior Fullstack Developer  
**Scope:** Menampilkan ringkasan perjalanan keuangan bulanan dan grafik pertumbuhan keuangan tahunan pada halaman dashboard admin.

---

## 1. Tujuan

Membangun dashboard admin yang menampilkan summary keuangan bulan berjalan dan visualisasi pertumbuhan pemasukan serta pengeluaran dalam satu tahun. Dashboard harus membantu user membaca kondisi keuangan secara cepat melalui card-board dan grafik double-line-chart.

Fitur ini hanya berfokus pada data transaksi dari tabel `transaction` dan tidak mencakup fitur CRUD, export laporan, filter kategori, autentikasi, atau perubahan struktur database.

## 2. Ruang Lingkup

Implementasi dilakukan menggunakan pola MVC pada artefak yang sudah tersedia:

- **Model:** `App\Models\TransactionModel`
- **Controller:** `App\Http\Controllers\DashboardController`
- **View:** `resources/views/admin/dashboard.blade.php`
- **Route:** route dashboard admin yang mengarah ke `DashboardController`
- **Library Chart:** Chart.js untuk visualisasi grafik tahunan

Jika route dashboard belum tersedia, implementer cukup menambahkan route admin dashboard yang mengarah ke controller ini. Jangan membuat controller, model, atau view dashboard baru.

## 3. Sumber Data

Seluruh data dashboard bersumber dari tabel `transaction` melalui `TransactionModel`.

Field utama yang digunakan:

- `amount` sebagai nilai uang transaksi
- `type` sebagai jenis transaksi, dengan nilai valid `income` dan `expense`
- `date` sebagai acuan periode bulan dan tahun

Interpretasi data:

- `type = expense` digunakan untuk seluruh data pengeluaran
- `type = income` digunakan untuk seluruh data pemasukan atau pendapatan
- Perhitungan bulan berjalan menggunakan bulan dan tahun dari tanggal saat user membuka dashboard
- Perhitungan grafik tahunan menggunakan tahun yang dipilih dari dropdown

## 4. Kebutuhan Data Summary Bulanan

Dashboard wajib menampilkan 4 card-board utama:

- **Total pengeluaran bulan saat ini:** jumlah record transaksi dengan `type = expense` pada bulan berjalan
- **Jumlah pengeluaran bulan saat ini:** total nominal `amount` transaksi dengan `type = expense` pada bulan berjalan
- **Total pemasukan bulan saat ini:** jumlah record transaksi dengan `type = income` pada bulan berjalan
- **Jumlah pemasukan bulan saat ini:** total nominal `amount` transaksi dengan `type = income` pada bulan berjalan

Aturan tampilan:

- Nilai uang ditampilkan dalam format Rupiah
- Jika tidak ada transaksi, tampilkan nilai `0` atau `Rp 0`
- Label card harus jelas dan membedakan antara total transaksi dan total nominal uang

## 5. Kebutuhan Grafik Tahunan

Di bawah group card-board, tampilkan grafik menggunakan Chart.js dalam bentuk double-line-chart.

Grafik wajib memiliki:

- Sumbu X berupa 12 bulan, dari Januari sampai Desember
- Line pertama untuk total nominal pengeluaran per bulan
- Line kedua untuk total nominal pemasukan per bulan
- Data berasal dari tabel `transaction` berdasarkan tahun yang dipilih
- Bulan tanpa transaksi tetap tampil dengan nilai `0`

Tujuan grafik:

- Menunjukkan pertumbuhan atau perubahan pengeluaran dan pemasukan sepanjang satu tahun
- Memudahkan user membandingkan tren pemasukan dan pengeluaran setiap bulan

## 6. Dropdown Pilihan Tahun

Sediakan dropdown pilihan tahun untuk mengubah data grafik tahunan pendapatan dan pengeluaran.

Aturan dropdown:

- Pilihan tahun diambil dari data tahun yang tersedia pada kolom `transaction.date`
- Tahun saat ini harus tetap tersedia sebagai pilihan meskipun belum ada transaksi pada tahun tersebut
- Default selected year adalah tahun saat user membuka dashboard
- Setiap perubahan dropdown wajib memicu request Ajax ke endpoint yang berada di `DashboardController`
- Endpoint Ajax menerima parameter `year` dan mengembalikan data chart tahunan dalam format JSON
- Setelah response berhasil diterima, view memperbarui dataset Chart.js tanpa melakukan full page reload
- Jika user memilih tahun yang tidak valid atau tidak tersedia, fallback ke tahun saat ini

Catatan: Scope dropdown hanya untuk grafik tahunan. Card-board bulanan tetap menggunakan bulan berjalan, bukan mengikuti pilihan tahun grafik.

## 7. Alur MVC

### Model

Gunakan `TransactionModel` sebagai representasi data transaksi. Tidak perlu membuat model baru.

Model cukup digunakan untuk query aggregate:

- Count transaksi berdasarkan type dan periode
- Sum nominal berdasarkan type dan periode
- Grouping nominal berdasarkan bulan untuk grafik tahunan
- Distinct year dari kolom `date` untuk dropdown

### Controller

`DashboardController` bertanggung jawab sebagai pusat orkestrasi data dashboard.

Controller perlu menyiapkan data berikut untuk view:

- `monthlyExpenseCount`
- `monthlyExpenseAmount`
- `monthlyIncomeCount`
- `monthlyIncomeAmount`
- `selectedYear`
- `availableYears`
- `chartLabels`
- `expenseChartData`
- `incomeChartData`

Controller juga harus menyediakan endpoint Ajax khusus untuk mengambil ulang data chart tahunan berdasarkan `year`.

Response endpoint Ajax minimal berisi:

- `selectedYear`
- `chartLabels`
- `expenseChartData`
- `incomeChartData`

Controller harus menjaga agar data yang dikirim ke view maupun response Ajax sudah siap render, sehingga Blade tidak berisi query database atau logika aggregate yang kompleks.

### View

`resources/views/admin/dashboard.blade.php` bertanggung jawab untuk tampilan saja.

View perlu:

- Mengganti placeholder card lama dengan 4 card-board sesuai summary bulanan
- Menampilkan dropdown pilihan tahun di area grafik
- Menampilkan canvas Chart.js untuk double-line-chart
- Chart.js sudah diinstall dan dipersiapkan sebelumnya (v^4.5.1). Cek di /package.json
- Menginisialisasi Chart.js menggunakan data yang dikirim dari controller
- Menangani event perubahan dropdown tahun dengan Ajax ke endpoint `DashboardController`
- Memperbarui label dan dataset Chart.js dari response Ajax tanpa reload halaman
- Menjaga tampilan tetap konsisten dengan layout admin dan styling yang sudah ada

## 8. Perilaku Halaman

Saat dashboard dibuka:

- Sistem menghitung summary transaksi untuk bulan berjalan
- Sistem menentukan tahun grafik default dari tahun saat ini
- Sistem menampilkan card-board dan grafik tahunan sesuai data yang tersedia

Saat user memilih tahun pada dropdown:

- View mengirim request Ajax ke endpoint chart tahunan di `DashboardController` dengan parameter `year`
- Controller memuat ulang data grafik berdasarkan tahun tersebut dan mengembalikan response JSON
- View memperbarui line pendapatan dan pengeluaran pada Chart.js tanpa full page reload
- Card-board bulanan tetap menampilkan data bulan berjalan

## 9. Batasan Implementasi

- Jangan mengubah struktur tabel `transaction`
- Jangan membuat fitur CRUD transaksi di scope ini
- Jangan membuat dashboard view baru
- Jangan membuat controller dashboard baru
- Jangan menambahkan filter kategori, export PDF/Excel, atau laporan detail
- Jangan memindahkan business logic aggregate ke Blade
- Jangan memakai data dummy jika data dari database sudah tersedia
- Jangan mengubah halaman lain kecuali diperlukan untuk route dashboard

## 10. Acceptance Criteria

- Dashboard menggunakan pola MVC dengan `TransactionModel`, `DashboardController`, dan `dashboard.blade.php`
- Empat card-board summary bulanan tampil sesuai data dari tabel `transaction`
- Total record pengeluaran dan pemasukan bulan berjalan dihitung dengan benar
- Total nominal pengeluaran dan pemasukan bulan berjalan dihitung dengan benar
- Grafik double-line-chart Chart.js tampil di bawah card-board
- Grafik memiliki 12 label bulan dan dua line: pengeluaran dan pemasukan
- Dropdown tahun tersedia dan memuat tahun dari data transaksi plus tahun saat ini
- Pemilihan tahun mengirim request Ajax ke endpoint `DashboardController`
- Response Ajax berhasil memperbarui chart tahunan tanpa reload halaman
- Pemilihan tahun mengubah data grafik tanpa mengubah summary bulanan
- Bulan tanpa transaksi pada grafik tetap bernilai `0`
- Nilai uang tampil dalam format Rupiah
- Halaman tetap responsive dan mengikuti visual admin dashboard yang sudah ada

## 11. Catatan untuk Implementer

Prioritaskan implementasi yang sederhana, eksplisit, dan mudah diverifikasi. Gunakan query aggregate yang jelas di controller, kirim data siap pakai ke Blade, dan hindari abstraksi berlebihan karena fitur ini masih berada dalam scope dashboard summary.

Jika ditemukan kebutuhan di luar scope dokumen ini, hentikan implementasi tambahan dan minta konfirmasi terlebih dahulu sebelum melanjutkan.
