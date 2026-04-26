@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_header', 'Dashboard Admin')
@section('page_subheader', 'Base layout admin untuk seluruh halaman aplikasi.')

@section('content')
    <div class="space-y-5">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Transaksi</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">1.245</p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Pendapatan</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">Rp 248jt</p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Pengeluaran</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">Rp 129jt</p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Saldo</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">Rp 119jt</p>
            </article>
        </section>

        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Area Konten Fleksibel</h2>
                    <p class="text-sm text-slate-500">
                        Blok ini disiapkan sebagai contoh area isi yang dapat diganti sesuai halaman.
                    </p>
                </div>
                <button
                    class="inline-flex items-center justify-center rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-rose-600"
                    data-modal-open="profile-modal"
                    type="button"
                >
                    Buka Modal Data Diri
                </button>
            </div>
        </section>
    </div>

@endsection

@section('modals')
    <x-ui.modal id="profile-modal" maxWidth="max-w-2xl" title="Input Data Diri">
        <form class="grid gap-4 sm:grid-cols-2">
            <label class="flex flex-col gap-1 text-sm text-slate-600">
                Nama
                <input class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100" placeholder="Masukkan nama" type="text">
            </label>

            <label class="flex flex-col gap-1 text-sm text-slate-600">
                Email
                <input class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100" placeholder="Masukkan email" type="email">
            </label>

            <label class="flex flex-col gap-1 text-sm text-slate-600">
                Nomor Telepon
                <input class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100" placeholder="Masukkan nomor telepon" type="tel">
            </label>

            <label class="flex flex-col gap-1 text-sm text-slate-600">
                Alamat
                <input class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100" placeholder="Masukkan alamat" type="text">
            </label>

            <div class="sm:col-span-2 flex items-center justify-end gap-2 border-t border-rose-100 pt-3">
                <button
                    class="rounded-xl border border-rose-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-rose-50"
                    data-modal-close
                    type="button"
                >
                    Batal
                </button>
                <button class="rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-rose-600" type="submit">
                    Simpan
                </button>
            </div>
        </form>
    </x-ui.modal>
@endsection
