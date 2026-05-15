@extends('layouts.admin')

@section('title', 'Manajemen Customer')
@section('page_header', 'Manajemen Customer')
@section('page_subheader', 'Kelola data customer melalui form modal berbasis Ajax.')

@section('content')
    <div class="space-y-5" data-customer-page data-customer-endpoint="{{ url('/admin/customer') }}">
        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Daftar Customer</h2>
                    <p class="text-sm text-slate-500">Tambah, ubah, atau hapus customer tanpa pindah halaman.</p>
                </div>

                <button
                    class="inline-flex items-center justify-center rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-rose-600"
                    data-customer-action="create"
                    type="button"
                >
                    Tambah Customer
                </button>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-rose-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-rose-100">
                    <thead class="bg-rose-50/70">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">Nama Lengkap</th>
                            <th class="px-4 py-3">Alamat Email</th>
                            <th class="px-4 py-3">Alamat</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-rose-100 text-sm text-slate-700" data-customer-table-body>
                        @forelse ($customers as $customer)
                            <tr data-customer-row-id="{{ $customer->id }}">
                                <td class="px-4 py-3">{{ $customer->full_name }}</td>
                                <td class="px-4 py-3">{{ $customer->email }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $customer->address ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                                            data-customer-action="edit"
                                            data-customer-address="{{ $customer->address }}"
                                            data-customer-email="{{ $customer->email }}"
                                            data-customer-fullname="{{ $customer->full_name }}"
                                            data-customer-id="{{ $customer->id }}"
                                            type="button"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                            data-customer-action="delete"
                                            data-customer-id="{{ $customer->id }}"
                                            type="button"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr data-customer-empty-row>
                                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="4">
                                    Data customer belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <button class="hidden" data-customer-modal-trigger data-modal-open="customer-form-modal" type="button"></button>
    </div>
@endsection

@section('modals')
    <x-ui.modal id="customer-form-modal" maxWidth="max-w-3xl" title="Form Customer">
        <form class="space-y-4" data-customer-form novalidate>
            <input data-customer-id name="customerId" type="hidden">

            <div class="space-y-4 md:grid md:grid-cols-2 md:gap-4 md:space-y-0">
                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Nama Lengkap</span>
                    <input
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-customer-input="fullname"
                        name="fullname"
                        placeholder="Masukkan nama lengkap"
                        required
                        type="text"
                    >
                    <span class="min-h-4 text-xs text-rose-600" data-customer-error="fullname"></span>
                </label>

                <label class="flex flex-col gap-1 text-sm text-slate-600">
                    <span class="font-medium">Alamat Email</span>
                    <input
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-customer-input="email"
                        name="email"
                        placeholder="customer@email.com"
                        required
                        type="email"
                    >
                    <span class="min-h-4 text-xs text-rose-600" data-customer-error="email"></span>
                </label>

                <label class="flex flex-col gap-1 text-sm text-slate-600 md:col-span-2">
                    <span class="font-medium">Alamat</span>
                    <textarea
                        class="min-h-28 rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-customer-input="address"
                        name="address"
                        placeholder="Masukkan alamat customer"
                    ></textarea>
                    <span class="min-h-4 text-xs text-rose-600" data-customer-error="address"></span>
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
                    data-customer-submit
                    type="submit"
                >
                    Simpan
                </button>
            </div>
        </form>
    </x-ui.modal>
@endsection
