@extends('layouts.admin')

@section('title', 'Invoice Baru')
@section('page_header', 'Invoice Baru')
@section('page_subheader', 'Susun invoice baru dengan format dokumen invoice.')

@section('content')
    <div class="space-y-6" data-invoice-form-page>
        <script data-invoice-customers type="application/json">@json($customers->values())</script>
        <script data-invoice-products type="application/json">@json($products->values())</script>

        <form data-customer-store-endpoint="{{ route('admin.invoice.customer.store') }}" data-invoice-form novalidate>
            <section class="rounded-2xl border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
                <div class="space-y-10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="inline-flex items-center gap-3">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-lg font-black tracking-wide text-rose-700">
                                NF
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nihons Finance</p>
                                <p class="text-sm text-slate-600">Invoice Workspace</p>
                            </div>
                        </div>

                        <h2 class="text-right text-3xl font-black uppercase tracking-[0.2em] text-slate-800 sm:text-4xl">
                            Invoice
                        </h2>
                    </div>

                    <div class="border-t border-rose-100/80 pt-8">
                        <div class="grid grid-cols-1 gap-7 lg:grid-cols-2">
                            <div class="space-y-3">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Customer</h3>

                                <div class="space-y-3" data-customer-trigger-wrap>
                                    <button
                                        class="inline-flex items-center justify-center rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-rose-600"
                                        data-invoice-action="activate-customer-picker"
                                        type="button"
                                    >
                                        Add Customer
                                    </button>
                                </div>

                                <div class="relative hidden space-y-2" data-customer-picker-wrap>
                                    <input name="customer_id" type="hidden" value="" data-invoice-customer-id>

                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Pilih Customer</p>
                                        <button
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-rose-100 text-slate-500 transition hover:bg-rose-50"
                                            data-invoice-action="close-customer-picker"
                                            type="button"
                                        >
                                            ×
                                        </button>
                                    </div>

                                    <button
                                        aria-expanded="false"
                                        class="flex w-full items-center justify-between rounded-xl border border-rose-100 bg-white px-3 py-2.5 text-left text-sm text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                                        data-invoice-customer-trigger
                                        type="button"
                                    >
                                        <span data-invoice-customer-selected-label>Pilih customer</span>
                                        <span class="text-slate-400">⌄</span>
                                    </button>

                                    <div class="absolute left-0 right-0 top-[calc(100%+0.35rem)] z-20 hidden rounded-xl border border-rose-100 bg-white p-3 shadow-lg" data-invoice-customer-dropdown>
                                        <input
                                            class="mb-2 w-full rounded-lg border border-rose-100 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                                            data-invoice-customer-search
                                            placeholder="Cari customer..."
                                            type="text"
                                        >
                                        <p class="mb-2 text-xs text-slate-500" data-invoice-customer-option-count>0 customer ditemukan</p>

                                        <div class="max-h-52 overflow-y-auto rounded-lg border border-rose-100">
                                            <ul class="divide-y divide-rose-100 text-sm text-slate-700" data-invoice-customer-options></ul>
                                        </div>

                                        <a
                                            class="mt-2 hidden text-xs font-medium lowercase tracking-wide text-rose-600 underline decoration-rose-200 underline-offset-2 hover:text-rose-700"
                                            data-invoice-action="open-add-customer-modal"
                                            href="#"
                                        >
                                            add-new-customer
                                        </a>
                                    </div>
                                </div>

                                <div class="hidden space-y-3 rounded-xl border border-rose-100 bg-rose-50/40 p-4" data-customer-detail-wrap>
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Customer Terpilih</p>
                                        <button
                                            class="rounded-lg border border-rose-200 px-3 py-1 text-xs font-medium text-rose-700 transition hover:bg-rose-100"
                                            data-invoice-action="edit-customer-selection"
                                            type="button"
                                        >
                                            Ganti Customer
                                        </button>
                                    </div>

                                    <div class="space-y-2 text-sm text-slate-700">
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-slate-400">Nama Lengkap</p>
                                            <p class="font-semibold" data-customer-detail-name>-</p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-slate-400">Email</p>
                                            <p class="font-medium" data-customer-detail-email>-</p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-slate-400">Alamat</p>
                                            <p class="font-medium" data-customer-detail-address>-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Invoice Meta</h3>
                                <div class="space-y-3">
                                    <label class="flex flex-col gap-1 text-sm text-slate-600">
                                        <span class="font-medium">Kode Invoice</span>
                                        <input
                                            class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                                            name="invoice_code"
                                            placeholder="INV-0001"
                                            type="text"
                                        >
                                    </label>

                                    <label class="flex flex-col gap-1 text-sm text-slate-600">
                                        <span class="font-medium">Issue Date</span>
                                        <input
                                            class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                                            data-invoice-issue-date
                                            name="issue_date"
                                            type="date"
                                        >
                                    </label>

                                    <label class="flex flex-col gap-1 text-sm text-slate-600">
                                        <span class="font-medium">Due Date</span>
                                        <input
                                            class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                                            data-invoice-due-date
                                            name="due_date"
                                            type="date"
                                        >
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-rose-100/80 pt-8">
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Invoice Items</h3>
                            <div class="overflow-x-auto rounded-xl border border-rose-100">
                                <table class="min-w-full divide-y divide-rose-100">
                                    <thead class="bg-rose-50/60">
                                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            <th class="px-4 py-3">Items</th>
                                            <th class="px-4 py-3">Qty</th>
                                            <th class="px-4 py-3">Unit Price</th>
                                            <th class="px-4 py-3">Total</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-rose-100 text-sm text-slate-700" data-invoice-items-body>
                                        <tr data-invoice-empty-row>
                                            <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="5">
                                                Belum ada item invoice. Klik Add Product untuk menambahkan baris pertama.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <button
                                class="inline-flex items-center justify-center rounded-xl border border-rose-200 px-4 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50"
                                data-invoice-add-product
                                type="button"
                            >
                                Add Product
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-rose-100/80 pt-8">
                        <div class="grid grid-cols-1 gap-7 lg:grid-cols-2">
                            <div class="space-y-3">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Langkah Pembayaran Invoice</h3>
                                <ol class="space-y-2 rounded-xl border border-rose-100 bg-rose-50/40 p-4 text-sm text-slate-700">
                                    <li>1. Login ke Internet Banking atau Mobile Banking Anda.</li>
                                    <li>2. Pilih menu transfer ke rekening tujuan invoice.</li>
                                    <li>3. Input nominal sesuai <strong>Total Payment</strong> invoice.</li>
                                    <li>4. Pada catatan transfer, masukkan kode invoice untuk referensi.</li>
                                    <li>5. Simpan bukti transfer dan konfirmasi pembayaran ke admin.</li>
                                </ol>
                            </div>

                            <div class="space-y-3">
                                <h3 class="text-right text-sm font-semibold uppercase tracking-wide text-slate-500">Ringkasan</h3>
                                <div class="space-y-2 rounded-xl border border-rose-100 bg-rose-50/40 p-4 text-sm text-slate-700">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Total Payment</span>
                                        <span class="font-semibold" data-invoice-total-payment>Rp 0,00</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Amount Due</span>
                                        <span class="font-semibold text-rose-700" data-invoice-amount-due>Rp 0,00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-rose-100/80 pt-8">
                        <div class="mx-auto w-full max-w-3xl">
                            <button
                                class="w-full rounded-xl bg-rose-500 px-4 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-rose-600"
                                type="submit"
                            >
                                submit invoice
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </form>

        <button class="hidden" data-invoice-open-customer-modal data-modal-open="add-customer-modal" type="button"></button>
    </div>
@endsection

@section('modals')
    <x-ui.modal id="add-customer-modal" maxWidth="max-w-lg" title="Tambah Customer">
        <form class="space-y-4" data-invoice-add-customer-form novalidate>
            <label class="flex flex-col gap-1 text-sm text-slate-600">
                <span class="font-medium">Nama Lengkap</span>
                <input
                    class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                    data-add-customer-input="full_name"
                    placeholder="Masukkan nama customer"
                    type="text"
                >
                <span class="min-h-4 text-xs text-rose-600" data-add-customer-error="full_name"></span>
            </label>

            <label class="flex flex-col gap-1 text-sm text-slate-600">
                <span class="font-medium">Email</span>
                <input
                    class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                    data-add-customer-input="email"
                    placeholder="customer@email.com"
                    type="email"
                >
                <span class="min-h-4 text-xs text-rose-600" data-add-customer-error="email"></span>
            </label>

            <label class="flex flex-col gap-1 text-sm text-slate-600">
                <span class="font-medium">Alamat</span>
                <textarea
                    class="min-h-24 rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                    data-add-customer-input="address"
                    placeholder="Alamat customer"
                ></textarea>
                <span class="min-h-4 text-xs text-rose-600" data-add-customer-error="address"></span>
            </label>

            <div class="flex items-center justify-end gap-2 border-t border-rose-100 pt-3">
                <button
                    class="rounded-xl border border-rose-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-rose-50"
                    data-modal-close
                    type="button"
                >
                    Batal
                </button>
                <button
                    class="rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-rose-600"
                    type="submit"
                >
                    Simpan Customer
                </button>
            </div>
        </form>
    </x-ui.modal>
@endsection
