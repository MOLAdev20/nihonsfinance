@extends('layouts.admin')

@section('title', 'Manajemen Produk')
@section('page_header', 'Manajemen Produk')
@section('page_subheader', 'Kelola produk dan jasa melalui form modal berbasis Ajax.')

@section('content')
    <div class="space-y-5" data-product-page data-product-endpoint="{{ url('/admin/product') }}">
        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Daftar Produk/Jasa</h2>
                    <p class="text-sm text-slate-500">Tambah, ubah, atau hapus data produk melalui modal form.</p>
                </div>

                <button
                    class="inline-flex items-center justify-center rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-rose-600"
                    data-product-action="create"
                    type="button"
                >
                    Tambah Produk
                </button>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-rose-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-rose-100">
                    <thead class="bg-rose-50/70">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nama Produk/Jasa</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Deskripsi Produk</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-rose-100 text-sm text-slate-700" data-product-table-body>
                        @forelse ($products as $product)
                            <tr data-product-row-id="{{ $product->id }}">
                                <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $product->id }}</td>
                                <td class="px-4 py-3">{{ $product->title }}</td>
                                <td class="whitespace-nowrap px-4 py-3">Rp {{ number_format((float) $product->price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->description ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                                            data-product-action="edit"
                                            data-product-description="{{ $product->description }}"
                                            data-product-id="{{ $product->id }}"
                                            data-product-price="{{ $product->price }}"
                                            data-product-title="{{ $product->title }}"
                                            type="button"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                            data-product-action="delete"
                                            data-product-id="{{ $product->id }}"
                                            type="button"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-product-empty-row>
                                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="5">
                                    Data produk belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <button class="hidden" data-modal-open="product-form-modal" data-product-modal-trigger type="button"></button>
    </div>
@endsection

@section('modals')
    <x-ui.modal id="product-form-modal" maxWidth="max-w-3xl" title="Form Produk">
        <form class="space-y-4" data-product-form novalidate>
            <input data-product-id name="productId" type="hidden">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Nama Produk/Jasa</span>
                    <input
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-product-input="title"
                        name="title"
                        placeholder="Masukkan nama produk/jasa"
                        type="text"
                    >
                    <span class="min-h-4 text-xs text-rose-600" data-product-error="title"></span>
                </label>

                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Harga</span>
                    <input
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-product-input="price"
                        min="0"
                        name="price"
                        placeholder="Masukkan harga"
                        step="0.01"
                        type="number"
                    >
                    <span class="min-h-4 text-xs text-rose-600" data-product-error="price"></span>
                </label>

                <label class="md:col-span-2 flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Deskripsi Produk</span>
                    <textarea
                        class="min-h-28 rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-product-input="description"
                        name="description"
                        placeholder="Masukkan deskripsi produk"
                    ></textarea>
                    <span class="min-h-4 text-xs text-rose-600" data-product-error="description"></span>
                </label>
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
                    data-product-submit
                    type="submit"
                >
                    Simpan
                </button>
            </div>
        </form>
    </x-ui.modal>
@endsection
