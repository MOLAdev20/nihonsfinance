@extends('layouts.admin')

@section('title', 'Manajemen Kategori')
@section('page_header', 'Manajemen Kategori')
@section('page_subheader', 'Kelola kategori transaksi menggunakan form modal berbasis Ajax.')

@section('content')
    <div class="space-y-5" data-category-page data-category-endpoint="{{ url('/admin/category') }}">
        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Daftar Kategori</h2>
                    <p class="text-sm text-slate-500">Tambah, ubah, atau hapus kategori transaksi tanpa pindah halaman.</p>
                </div>

                <button
                    class="inline-flex items-center justify-center rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-rose-600"
                    data-category-action="create"
                    type="button"
                >
                    Tambah Kategori
                </button>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-rose-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-rose-100">
                    <thead class="bg-rose-50/70">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-rose-100 text-sm text-slate-700" data-category-table-body>
                        @forelse ($categories as $category)
                            <tr data-category-row-id="{{ $category->id }}">
                                <td class="px-4 py-3">{{ $category->title }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                                            data-category-action="edit"
                                            data-category-id="{{ $category->id }}"
                                            data-category-title="{{ $category->title }}"
                                            data-category-type="{{ $category->type }}"
                                            type="button"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                            data-category-action="delete"
                                            data-category-id="{{ $category->id }}"
                                            type="button"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-category-empty-row>
                                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="3">
                                    Data kategori belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <button class="hidden" data-category-modal-trigger data-modal-open="category-form-modal" type="button"></button>
    </div>
@endsection

@section('modals')
    <x-ui.modal id="category-form-modal" maxWidth="max-w-2xl" title="Form Kategori">
        <form class="space-y-4" data-category-form novalidate>
            <input data-category-id name="categoryId" type="hidden">

            <div class="space-y-4 md:grid md:grid-cols-2 md:gap-4 md:space-y-0">
                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Nama Kategori</span>
                    <input
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-category-input="title"
                        name="title"
                        placeholder="Masukkan nama kategori"
                        required
                        type="text"
                    >
                    <span class="min-h-4 text-xs text-rose-600" data-category-error="title"></span>
                </label>

                <div class="flex flex-col gap-1 text-sm text-slate-600 md:col-span-1">
                    <span class="font-medium">Jenis Kategori</span>
                    <div class="rounded-xl border border-rose-100 p-3" data-category-type-wrapper>
                        <div class="flex flex-col gap-2">
                            <label class="inline-flex items-center gap-2">
                                <input
                                    class="h-4 w-4 border border-rose-200 text-rose-500 focus:ring-rose-300"
                                    data-category-input="type"
                                    name="type"
                                    required
                                    type="radio"
                                    value="income"
                                >
                                <span>Pemasukan</span>
                            </label>

                            <label class="inline-flex items-center gap-2">
                                <input
                                    class="h-4 w-4 border border-rose-200 text-rose-500 focus:ring-rose-300"
                                    data-category-input="type"
                                    name="type"
                                    type="radio"
                                    value="expense"
                                >
                                <span>Pengeluaran</span>
                            </label>
                        </div>
                    </div>
                    <span class="min-h-4 text-xs text-rose-600" data-category-error="type"></span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-rose-100 pt-3">
                <button
                    class="rounded-xl border border-rose-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-rose-50"
                    data-modal-close
                    type="button"
                >
                    Batal
                </button>
                <button
                    class="rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-rose-600 disabled:cursor-not-allowed disabled:opacity-70"
                    data-category-submit
                    type="submit"
                >
                    Simpan
                </button>
            </div>
        </form>
    </x-ui.modal>
@endsection
