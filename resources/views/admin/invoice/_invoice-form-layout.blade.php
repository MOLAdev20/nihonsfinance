@php
    $selectedCustomerId = (string) old('customer_id', $invoice?->customer_id ?? '');
    $rawItems = old('items');
    if (!is_array($rawItems)) {
        $rawItems = $invoice
            ? $invoice->lines->map(fn ($line) => ['product_id' => $line->product_id, 'qty' => $line->qty])->all()
            : [];
    }

    $formItems = collect($rawItems)
        ->values()
        ->map(function ($item) {
            return [
                'product_id' => data_get($item, 'product_id', ''),
                'qty' => data_get($item, 'qty', 1),
            ];
        })
        ->all();

    if (count($formItems) === 0) {
        $formItems = [['product_id' => '', 'qty' => 1]];
    }

    $selectedCustomer = $customers->firstWhere('id', (int) $selectedCustomerId);
    $selectedCustomerPayload = $selectedCustomer
        ? [
            'id' => (string) $selectedCustomer->id,
            'full_name' => $selectedCustomer->full_name,
            'email' => $selectedCustomer->email,
            'address' => $selectedCustomer->address,
        ]
        : null;
@endphp

<div class="space-y-6" data-invoice-form-page>
    @if (session('successMessage'))
        <div class="hidden" data-invoice-success-message="{{ session('successMessage') }}"></div>
    @endif

    <script data-invoice-customers type="application/json">@json($customers->values())</script>
    <script data-invoice-products type="application/json">@json($products->values())</script>
    <script data-invoice-selected-customer type="application/json">
        @json($selectedCustomerPayload)
    </script>

    <form
        action="{{ $formAction }}"
        data-customer-store-endpoint="{{ route('admin.invoice.customer.store') }}"
        data-invoice-form
        method="POST"
        novalidate
    >
        @csrf
        @if ($formMethod !== 'POST')
            @method($formMethod)
        @endif

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
                                <input name="customer_id" type="hidden" value="{{ $selectedCustomerId }}" data-invoice-customer-id>

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
                            @error('customer_id')
                                <p class="text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Invoice Meta</h3>
                            <div class="space-y-3">
                                <label class="flex flex-col gap-1 text-sm text-slate-600">
                                    <span class="font-medium">Kode Invoice</span>
                                    <input
                                        class="{{ $errors->has('invoice_code') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : 'border-rose-100 focus:border-rose-300 focus:ring-rose-100' }} rounded-xl border bg-white px-3 py-2 text-slate-700 outline-none transition focus:ring-2"
                                        name="invoice_code"
                                        placeholder="INV-0001"
                                        type="text"
                                        value="{{ old('invoice_code', $invoice?->invoice_code) }}"
                                    >
                                    @error('invoice_code')
                                        <span class="text-xs text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="flex flex-col gap-1 text-sm text-slate-600">
                                    <span class="font-medium">Issue Date</span>
                                    <input
                                        class="{{ $errors->has('issue_date') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : 'border-rose-100 focus:border-rose-300 focus:ring-rose-100' }} rounded-xl border bg-white px-3 py-2 text-slate-700 outline-none transition focus:ring-2"
                                        data-invoice-issue-date
                                        name="issue_date"
                                        type="date"
                                        value="{{ old('issue_date', $invoice?->issue_date?->format('Y-m-d')) }}"
                                    >
                                    @error('issue_date')
                                        <span class="text-xs text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="flex flex-col gap-1 text-sm text-slate-600">
                                    <span class="font-medium">Due Date</span>
                                    <input
                                        class="{{ $errors->has('due_date') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : 'border-rose-100 focus:border-rose-300 focus:ring-rose-100' }} rounded-xl border bg-white px-3 py-2 text-slate-700 outline-none transition focus:ring-2"
                                        data-invoice-due-date
                                        name="due_date"
                                        type="date"
                                        value="{{ old('due_date', $invoice?->due_date?->format('Y-m-d')) }}"
                                    >
                                    @error('due_date')
                                        <span class="text-xs text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-rose-100/80 pt-8">
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Invoice Items</h3>
                        @error('items')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
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
                                    @foreach ($formItems as $itemIndex => $item)
                                        <tr data-invoice-item-row data-subtotal="0">
                                            <td class="px-4 py-3">
                                                <select
                                                    class="{{ $errors->has('items.' . $itemIndex . '.product_id') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : 'border-rose-100 focus:border-rose-300 focus:ring-rose-100' }} w-full rounded-xl bg-white px-3 py-2 text-slate-700 outline-none transition"
                                                    data-invoice-item-product
                                                    name="items[{{ $itemIndex }}][product_id]"
                                                >
                                                    <option value="">Pilih item</option>
                                                    @foreach ($products as $product)
                                                        <option
                                                            @selected((string) data_get($item, 'product_id') === (string) $product->id)
                                                            data-product-price="{{ $product->price }}"
                                                            value="{{ $product->id }}"
                                                        >
                                                            {{ $product->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('items.' . $itemIndex . '.product_id')
                                                    <span class="text-xs text-rose-600">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    class="{{ $errors->has('items.' . $itemIndex . '.qty') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : 'border-rose-100 focus:border-rose-300 focus:ring-rose-100' }} w-28 rounded-xl bg-white px-3 py-2 text-slate-700 outline-none transition"
                                                    data-invoice-item-qty
                                                    min="1"
                                                    name="items[{{ $itemIndex }}][qty]"
                                                    type="number"
                                                    value="{{ old('items.' . $itemIndex . '.qty', data_get($item, 'qty', 1)) }}"
                                                >
                                                @error('items.' . $itemIndex . '.qty')
                                                    <span class="text-xs text-rose-600">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-700" data-invoice-item-unit-price>
                                                Rp 0,00
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-800" data-invoice-item-total>
                                                Rp 0,00
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button
                                                    class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                                                    data-invoice-item-remove
                                                    type="button"
                                                >
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
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
                    <div class="mx-auto flex w-full max-w-3xl items-center gap-3">
                        <a
                            class="inline-flex items-center justify-center rounded-xl border border-rose-100 px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-rose-50"
                            href="{{ route('admin.invoice.index') }}"
                        >
                            Kembali
                        </a>
                        <button
                            class="w-full rounded-xl bg-rose-500 px-4 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-rose-600"
                            type="submit"
                        >
                            {{ $submitLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </form>

    <button class="hidden" data-invoice-open-customer-modal data-modal-open="add-customer-modal" type="button"></button>
</div>
