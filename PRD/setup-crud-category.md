# Document Plan: CRUD Category Management (Ajax-Based)

**Role:** Technical Architect / PM
**Scope:** Implementasi fitur CRUD Kategori menggunakan Ajax, Modal form, dengan arsitektur MVC.

---

## 1. Tujuan & Ruang Lingkup

Membangun modul manajemen kategori (CRUD) yang sepenuhnya responsif dan berjalan via Ajax. Proses input dan edit wajib menggunakan satu Modal dinamis untuk memastikan _User Experience_ yang mulus tanpa _page reload_.

## 2. Arsitektur & File Structure

Kerjakan strictly menggunakan pola MVC pada file berikut:

- **View:** `admin/category.blade.php`
- **Controller:** `categoryController.php`
- **Model:** `categoryModel.php`
- **Route:** Berada di dalam prefix `admin/category`

_Note: Jangan mengubah file di luar scope ini selain untuk meregistrasikan routing._

## 3. Spesifikasi UI & View (`admin/category.blade.php`)

- **Layouting:** Wajib _extend_ base layout yang sama persis dengan `product.blade.php`.
- **Komponen Utama:**
    - Tabel data kategori (menampilkan Nama, Jenis, dan Aksi).
    - Tombol trigger untuk membuka Modal "Tambah Kategori".
- **Spesifikasi Modal & Responsivitas:**
    - Gunakan 1 Modal yang sama untuk operasi Insert dan Edit.
    - Form layout wajib responsif:
        - Mobile screen: Gunakan pola `block` (elemen berjejer berurutan dari atas ke bawah).
        - Medium-Large screen: Gunakan pola `grid`.

## 4. Spesifikasi Form

Di dalam Modal, buat form dengan detail berikut:

1. **Input Nama Kategori:**
    - Type: `text`
    - Label: Nama Kategori
    - Atribut: `required`
2. **Input Jenis Kategori:**
    - Type: `radio`
    - Label: Jenis Kategori
    - Opsi 1: Label "Pemasukan", Value `income`
    - Opsi 2: Label "Pengeluaran", Value `expense`
    - Atribut: `required`

## 5. Aturan Validasi & Error Handling (Strict)

Proses validasi wajib dilakukan di sisi server (Backend/Controller).

- **Jika Validasi Gagal (Response 422):**
    - Tampilkan pesan kesalahan dari server ke bawah masing-masing inputan yang bersangkutan.
    - Pesan error wajib menggunakan **bahasa Indonesia formal dan singkat** (contoh: "Bidang ini wajib diisi.").
    - Teks error menggunakan styling ukuran kecil (class `text-xs`).
    - Ubah warna _border_ inputan yang bermasalah menjadi merah.

## 6. Business Logic & Interaksi Ajax

Semua operasi wajib menggunakan konvensi penamaan **camelCase** untuk variabel custome, fungsi custome, dll (selain nama class)

- **Insert & Edit (Submit Form):**
    - Kirim data via Ajax ke endpoint terkait.
    - Jika sukses: Tutup modal, _refresh_ data tabel secara asinkron, dan panggil notifikasi/alert dari ekosistem **Bell JS** (https://belljs.vercel.app/).
- **Delete Operation:**
    - Saat user klik hapus, cegah aksi default.
    - Tampilkan konfirmasi menggunakan bawaan browser (`window.confirm()`).
    - Jika _True_, proses hapus via Ajax. Jika sukses, hapus baris dari DOM dan tampilkan alert **Bell JS**.
