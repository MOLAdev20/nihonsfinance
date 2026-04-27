@extends('layouts.admin')

@section('title', 'Manajemen Transaksi')
@section('page_header', 'Manajemen Transaksi')
@section('page_subheader', 'Kelola data transaksi melalui form modal berbasis Ajax.')

@section('content')
    <div class="space-y-5" data-transaction-page data-transaction-endpoint="{{ url('/admin/transaction') }}">
        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Daftar Transaksi</h2>
                    <p class="text-sm text-slate-500">Tambah, ubah, atau hapus data transaksi tanpa pindah halaman.</p>
                </div>

                <button
                    class="inline-flex items-center justify-center rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-rose-600"
                    data-transaction-action="create"
                    type="button"
                >
                    Tambah Transaksi
                </button>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-rose-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-rose-100">
                    <thead class="bg-rose-50/70">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">Transaksi</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-rose-100 text-sm text-slate-700" data-transaction-table-body>
                        @forelse ($transactions as $transaction)
                            <tr data-transaction-row-id="{{ $transaction->id }}">
                                <td class="px-4 py-3">{{ $transaction->category?->title ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    Rp {{ number_format((float) $transaction->amount, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    {{ $transaction->date?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $transaction->description }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                                            data-transaction-action="edit"
                                            data-transaction-amount="{{ $transaction->amount }}"
                                            data-transaction-category-id="{{ $transaction->category_id }}"
                                            data-transaction-date="{{ $transaction->date?->format('Y-m-d\TH:i') }}"
                                            data-transaction-description="{{ $transaction->description }}"
                                            data-transaction-id="{{ $transaction->id }}"
                                            data-transaction-type="{{ $transaction->type }}"
                                            type="button"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                            data-transaction-action="delete"
                                            data-transaction-id="{{ $transaction->id }}"
                                            type="button"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-transaction-empty-row>
                                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="6">
                                    Data transaksi belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <button
            class="hidden"
            data-modal-open="transaction-form-modal"
            data-transaction-modal-trigger
            type="button"
        ></button>
    </div>
@endsection

@section('modals')
    <x-ui.modal id="transaction-form-modal" maxWidth="max-w-4xl" title="Form Transaksi">
        <form class="space-y-4" data-transaction-form novalidate>
            <input data-transaction-id name="transactionId" type="hidden">
            <input data-transaction-input="type" name="type" type="hidden">

            <div class="space-y-4 md:grid md:grid-cols-2 md:gap-4 md:space-y-0">
                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Transaksi</span>
                    <select
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-transaction-input="category_id"
                        name="category_id"
                        required
                    >
                        <option value="">Pilih kategori transaksi</option>
                        @foreach ($categories as $category)
                            <option
                                data-category-type="{{ $category->type }}"
                                value="{{ $category->id }}"
                            >
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                    <span class="min-h-4 text-xs text-rose-600" data-transaction-error="category_id"></span>
                </label>

                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Jumlah</span>
                    <input
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-transaction-input="amount"
                        min="0.01"
                        name="amount"
                        placeholder="Masukkan jumlah transaksi"
                        required
                        step="0.01"
                        type="number"
                    >
                    <span class="min-h-4 text-xs text-rose-600" data-transaction-error="amount"></span>
                </label>

                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Tanggal Transaksi</span>
                    <input
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-transaction-input="date"
                        name="date"
                        required
                        type="datetime-local"
                    >
                    <span class="min-h-4 text-xs text-rose-600" data-transaction-error="date"></span>
                </label>

                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Jenis Transaksi</span>
                    <input
                        class="rounded-xl border border-rose-100 bg-slate-50 px-3 py-2 text-slate-700 outline-none transition"
                        data-transaction-input="type_display"
                        readonly
                        type="text"
                        value="-"
                    >
                    <span class="min-h-4 text-xs text-rose-600" data-transaction-error="type"></span>
                </label>

                <label class="md:col-span-2 flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Deskripsi</span>
                    <textarea
                        class="min-h-28 rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-transaction-input="description"
                        name="description"
                        placeholder="Masukkan deskripsi transaksi"
                        required
                    ></textarea>
                    <span class="min-h-4 text-xs text-rose-600" data-transaction-error="description"></span>
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
                    data-transaction-submit
                    type="submit"
                >
                    Simpan
                </button>
            </div>
        </form>
    </x-ui.modal>
@endsection
