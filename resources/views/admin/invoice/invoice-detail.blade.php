@extends('layouts.admin')

@section('title', 'Detail Invoice')
@section('page_header', 'Detail Invoice')
@section('page_subheader', 'Preview data invoice secara read-only.')

@section('content')
    <div class="space-y-5" data-invoice-list-page>
        @if (session('successMessage'))
            <div class="hidden" data-invoice-success-message="{{ session('successMessage') }}"></div>
        @endif

        <section class="rounded-2xl border border-rose-100 bg-white p-6 shadow-sm sm:p-8">
            <div class="space-y-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nihons Finance</p>
                        <p class="text-sm text-slate-600">Invoice Preview</p>
                    </div>
                    <h2 class="text-right text-3xl font-black uppercase tracking-[0.2em] text-slate-800 sm:text-4xl">
                        Invoice
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-6 border-t border-rose-100/80 pt-6 lg:grid-cols-2">
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Customer</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $invoice->customer?->full_name ?? '-' }}</p>
                        <p class="text-sm text-slate-600">{{ $invoice->customer?->email ?? '-' }}</p>
                        <p class="text-sm text-slate-600">{{ $invoice->customer?->address ?? '-' }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Invoice Meta</p>
                        <p class="text-sm text-slate-700"><span class="font-medium">Kode:</span> {{ $invoice->invoice_code }}</p>
                        <p class="text-sm text-slate-700"><span class="font-medium">Issue Date:</span> {{ $invoice->issue_date?->format('d/m/Y') }}</p>
                        <p class="text-sm text-slate-700"><span class="font-medium">Due Date:</span> {{ $invoice->due_date?->format('d/m/Y') }}</p>
                        <p class="text-sm text-slate-700"><span class="font-medium">Status:</span> {{ strtoupper($invoice->status) }}</p>
                    </div>
                </div>

                <div class="border-t border-rose-100/80 pt-6">
                    <div class="overflow-x-auto rounded-xl border border-rose-100">
                        <table class="min-w-full divide-y divide-rose-100">
                            <thead class="bg-rose-50/70">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <th class="px-4 py-3">Items</th>
                                    <th class="px-4 py-3">Qty</th>
                                    <th class="px-4 py-3">Unit Price</th>
                                    <th class="px-4 py-3">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rose-100 text-sm text-slate-700">
                                @forelse ($invoice->lines as $line)
                                    <tr>
                                        <td class="px-4 py-3">{{ $line->product?->title ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $line->qty }}</td>
                                        <td class="px-4 py-3">Rp {{ number_format((float) $line->unit_price, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 font-semibold">Rp {{ number_format((float) $line->subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-6 text-center text-sm text-slate-500" colspan="4">Item invoice tidak tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-rose-100/80 pt-6">
                    <div class="ml-auto max-w-xs space-y-2 rounded-xl border border-rose-100 bg-rose-50/40 p-4 text-sm text-slate-700">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Total Payment</span>
                            <span class="font-semibold">Rp {{ number_format((float) $invoice->total_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Amount Due</span>
                            <span class="font-semibold text-rose-700">Rp {{ number_format((float) $invoice->total_amount, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-2">
            <a
                class="inline-flex items-center justify-center rounded-xl border border-rose-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-rose-50"
                href="{{ route('admin.invoice.index') }}"
            >
                Kembali ke Daftar
            </a>
            <a
                class="inline-flex items-center justify-center rounded-xl border border-rose-200 px-4 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50"
                href="{{ route('admin.invoice.edit', $invoice) }}"
            >
                Edit Invoice
            </a>
            <form action="{{ route('admin.invoice.destroy', $invoice) }}" data-invoice-delete-form method="POST">
                @csrf
                @method('DELETE')
                <button
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    type="submit"
                >
                    Hapus Invoice
                </button>
            </form>
        </div>
    </div>
@endsection
