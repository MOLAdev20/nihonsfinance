# PRD / Document Plan: CRUD Customer Berbasis Ajax

**Role:** Technical Architect / PM / Senior Fullstack Developer  
**Scope:** Membangun manajemen customer berbasis Ajax dengan form modal untuk tambah, edit, hapus, dan daftar customer pada halaman admin.

---

## 1. Tujuan

Membangun fitur CRUD customer pada halaman `admin/customer` agar user admin dapat mengelola data customer tanpa full page reload. Fitur harus memakai pola MVC, validasi sisi server, modal form untuk input dan edit, serta feedback operasi yang jelas melalui Bell Alert.

Fitur ini hanya berfokus pada pengelolaan data customer di tabel `customer`. Jangan mengerjakan fitur invoice, transaksi, dashboard, autentikasi, perubahan sidebar, atau perubahan struktur aplikasi lain di luar kebutuhan CRUD customer.

## 2. Ruang Lingkup

Implementasi dilakukan pada artefak berikut:

- **Route:** `admin/customer`
- **View:** `resources/views/admin/customer.blade.php`
- **Controller:** `app/Http/Controllers/CustomerController.php`
- **Model:** `app/Models/CustomerModel.php`
- **Frontend script:** file JavaScript halaman customer, mengikuti pola script halaman `product` atau `category`
- **Sidebar:** menu admin existing untuk menampilkan akses `Master Pelanggan`

Jika route `admin/customer` belum lengkap, implementer hanya boleh melengkapinya untuk kebutuhan CRUD customer:

- `GET /admin/customer` untuk daftar customer
- `POST /admin/customer` untuk tambah customer
- `PUT /admin/customer/{customer}` untuk update customer
- `DELETE /admin/customer/{customer}` untuk hapus customer

Jangan mengubah endpoint customer yang dipakai flow invoice kecuali memang route tersebut konflik langsung dengan route CRUD customer.

Menu customer wajib ditampilkan di sidebar admin dengan nama `Master Pelanggan` dan mengarah ke halaman `admin/customer`.

## 3. Referensi Pola Existing

Gunakan implementasi berikut sebagai acuan gaya dan pola:

- `resources/views/admin/product.blade.php` untuk layout tabel, section header, modal, tombol aksi, dan style visual
- `resources/views/admin/category.blade.php` untuk pola CRUD Ajax yang sederhana
- `resources/page/product.js` atau `resources/page/category.js` untuk pola Ajax, Bell Alert, validasi error, konfirmasi hapus, dan refresh/render tabel
- `app/Http/Controllers/CategoryController.php` atau `ProductController.php` untuk pola controller CRUD JSON

View customer harus terasa konsisten dengan layout admin yang sudah ada. Jangan membuat layout baru.

## 4. Data Customer

Field form customer:

- `fullname`
  - Label: `Nama Lengkap`
  - Input type: `text`
  - Wajib diisi
- `email`
  - Label: `Alamat Email`
  - Input type: `email`
  - Wajib diisi
- `address`
  - Label: `Alamat`
  - Input type: `textarea`
  - Opsional

Catatan penamaan data:

- Requirement bisnis menyebut field `fullname`.
- Jika struktur tabel/model existing memakai kolom `full_name`, lakukan mapping secara jelas di controller atau layer request/response.
- Response JSON ke frontend harus konsisten agar proses render tabel dan pengisian form edit tidak ambigu.

## 5. Kebutuhan Halaman

Halaman `admin/customer` wajib menampilkan:

- Header halaman manajemen customer
- Tombol `Tambah Customer`
- Tabel daftar customer
- Aksi `Edit` dan `Hapus` pada setiap row
- Empty state jika data customer belum tersedia
- Modal form untuk tambah customer
- Modal form yang sama untuk edit customer

Kolom tabel minimum:

- Nama Lengkap
- Alamat Email
- Alamat
- Aksi

Aturan tampilan:

- Gunakan layout base admin yang sudah ada
- Ikuti visual style `product.blade.php`
- Tabel harus tetap nyaman dibaca pada layar kecil, minimal dengan horizontal scroll jika dibutuhkan
- Empty state harus memakai pesan singkat, misalnya `Data customer belum tersedia.`

## 6. Kebutuhan Sidebar Menu

Sidebar admin wajib menampilkan menu customer dengan label `Master Pelanggan`.

Aturan sidebar:

- Menu `Master Pelanggan` mengarah ke route halaman `admin/customer`
- Menu memiliki active state saat user berada di halaman manajemen customer
- Posisi menu mengikuti pola navigasi admin existing dan ditempatkan secara wajar bersama menu master data lain
- Jangan menggunakan link placeholder seperti `#`
- Jangan membuat group menu baru jika struktur sidebar existing tidak membutuhkannya
- Styling, ikon jika ada, dan active state harus mengikuti pola sidebar yang sudah ada

Tujuan menu ini adalah memberi akses langsung ke halaman CRUD customer dari navigasi admin.

## 7. Kebutuhan Form Modal

Form tambah dan edit menggunakan modal yang sama.

Perilaku modal:

- Klik `Tambah Customer` membuka modal dalam mode tambah
- Klik `Edit` membuka modal dalam mode edit dan mengisi field dari data row terkait
- Mode tambah mengosongkan form dan hidden id
- Mode edit menyimpan id customer yang akan di-update
- Tombol submit berubah konteks jika diperlukan, misalnya `Simpan` untuk tambah dan `Perbarui` untuk edit
- Setelah operasi berhasil, modal ditutup dan form di-reset

Layout form:

- Mobile: field tampil block dari atas ke bawah
- Medium-large: gunakan grid yang rapi
- Field `address` boleh dibuat full width pada layout grid
- Teks error validasi memakai ukuran `text-xs` dan berada tepat di bawah field terkait

## 8. Validasi Server-Side

Validasi wajib dilakukan di sisi server melalui controller.

Aturan validasi:

- `fullname` / `full_name`: required, string, maksimal 255 karakter
- `email`: required, email valid, maksimal 255 karakter
- `address`: nullable, string

Jika email harus unik berdasarkan aturan bisnis existing, terapkan validasi unique dengan aturan update yang mengabaikan record customer yang sedang diedit.

Pesan validasi harus singkat, formal, dan berbahasa Indonesia. Contoh arah pesan:

- Nama lengkap wajib diisi.
- Nama lengkap maksimal 255 karakter.
- Alamat email wajib diisi.
- Format alamat email tidak valid.
- Alamat email sudah terdaftar.
- Alamat tidak valid.

Saat validasi gagal:

- Controller mengembalikan response JSON status `422`
- Frontend menampilkan pesan error di bawah field terkait
- Border input field yang gagal validasi berubah menjadi merah
- Error lama dibersihkan saat user mulai mengubah input terkait atau saat modal dibuka ulang

## 9. Kebutuhan Ajax

Seluruh operasi CRUD pada halaman customer dilakukan berbasis Ajax.

Perilaku Ajax:

- `GET /admin/customer` menampilkan view saat request normal
- `GET /admin/customer` dapat mengembalikan JSON daftar customer saat request mengharapkan JSON, jika pola refresh tabel membutuhkan ini
- `POST /admin/customer` menyimpan customer baru dan mengembalikan JSON
- `PUT /admin/customer/{customer}` memperbarui customer dan mengembalikan JSON
- `DELETE /admin/customer/{customer}` menghapus customer dan mengembalikan JSON

Response sukses minimal berisi:

- `message`
- `customer` untuk operasi tambah dan edit

Response delete minimal berisi:

- `message`

Frontend harus menyertakan CSRF token dan header `Accept: application/json`.

## 10. Feedback User

Gunakan Bell Alert dari package yang sudah terinstall untuk operasi sukses:

- Customer berhasil ditambahkan
- Customer berhasil diperbarui
- Customer berhasil dihapus

Untuk konfirmasi sebelum hapus, gunakan alert bawaan JavaScript:

- Gunakan `window.confirm`
- Jika user membatalkan, jangan kirim request delete
- Jika delete gagal, tampilkan pesan gagal yang singkat menggunakan alert bawaan browser

Jangan menambahkan library alert baru.

## 11. Alur MVC

### Model

`CustomerModel` menjadi representasi tabel `customer`.

Model bertanggung jawab pada konfigurasi dasar Eloquent:

- Nama tabel customer
- Field fillable customer
- Relasi existing tetap dipertahankan

Jangan memindahkan validasi request ke model.

### Controller

`CustomerController` menjadi pusat business logic CRUD customer.

Controller perlu menyediakan:

- `index` untuk mengambil daftar customer dan render view
- `store` untuk validasi dan insert customer
- `update` untuk validasi dan update customer
- `destroy` untuk hapus customer

Controller harus mengembalikan response JSON untuk operasi Ajax dan menjaga pesan validasi dalam bahasa Indonesia.

### View

`admin/customer.blade.php` bertanggung jawab pada struktur tampilan:

- Header dan tombol tambah
- Tabel customer
- Empty state
- Modal form
- Data attribute yang dibutuhkan JavaScript

Blade tidak boleh berisi query database atau business logic validasi.

### JavaScript

Script halaman customer bertanggung jawab pada interaksi browser:

- Membuka modal tambah/edit
- Mengisi form edit
- Mengirim request Ajax
- Menampilkan error validasi
- Mengubah border input menjadi merah saat validasi gagal
- Menutup modal setelah sukses
- Memperbarui tabel setelah create/update/delete
- Menampilkan Bell Alert saat sukses

Tambahkan loader script customer ke mekanisme page loader existing di `resources/js/app.js` jika pola aplikasi membutuhkannya.

## 12. Naming Convention

Seluruh custom variable dan function yang dibuat harus memakai camelCase.

Contoh arah penamaan:

- `customerPage`
- `customerForm`
- `customerEndpoint`
- `showSuccessAlert`
- `clearValidationErrors`
- `setEditMode`
- `renderCustomerRows`

Jangan memakai snake_case untuk nama variable atau function JavaScript custom.

## 13. Batasan Implementasi

- Jangan mengerjakan fitur di luar CRUD customer
- Jangan mengubah struktur database kecuali ada migration existing yang memang belum sesuai dan secara eksplisit dibutuhkan
- Jangan membuat layout admin baru
- Jangan menambahkan library alert baru
- Jangan memakai validasi hanya di sisi frontend
- Jangan menyimpan business logic database di Blade
- Jangan mengubah modul invoice, produk, kategori, transaksi, atau dashboard
- Jangan membuat fitur pencarian, pagination, export, import, filter, atau bulk action karena tidak termasuk scope

## 14. Acceptance Criteria

- Sidebar admin menampilkan menu `Master Pelanggan`
- Menu `Master Pelanggan` mengarah ke halaman `admin/customer`
- Menu `Master Pelanggan` memiliki active state saat halaman customer dibuka
- Route `admin/customer` dapat membuka halaman manajemen customer
- Halaman menggunakan `admin/customer.blade.php` dan layout admin existing
- Data customer ditampilkan dalam tabel dengan kolom Nama Lengkap, Alamat Email, Alamat, dan Aksi
- Tombol `Tambah Customer` membuka modal form kosong
- Tombol `Edit` membuka modal form berisi data customer terkait
- Form tambah dan edit memakai field `Nama Lengkap`, `Alamat Email`, dan `Alamat`
- Create customer berjalan via Ajax tanpa full page reload
- Update customer berjalan via Ajax tanpa full page reload
- Delete customer berjalan via Ajax setelah konfirmasi `window.confirm`
- Validasi server-side berjalan untuk field wajib dan format email
- Error validasi tampil tepat di bawah input terkait dengan ukuran `text-xs`
- Field yang gagal validasi memiliki border merah
- Operasi sukses menampilkan Bell Alert
- Tabel diperbarui setelah create, update, dan delete
- Empty state tampil saat tidak ada data
- Layout form responsive: block pada mobile dan grid pada medium-large
- Custom variable dan function yang dibuat memakai camelCase

## 15. Checklist Implementasi High Level

- Tambahkan menu `Master Pelanggan` pada sidebar admin existing
- Audit route `admin/customer` dan lengkapi endpoint CRUD jika belum tersedia
- Lengkapi `CustomerController` dengan method `index`, `store`, `update`, dan `destroy`
- Pastikan `CustomerModel` mendukung field customer yang dibutuhkan
- Buat `resources/views/admin/customer.blade.php` mengikuti pola `product.blade.php`
- Buat script Ajax khusus halaman customer mengikuti pola `category.js` atau `product.js`
- Daftarkan script customer pada loader JavaScript aplikasi jika diperlukan
- Uji alur tambah, edit, hapus, validasi gagal, empty state, dan responsive layout
