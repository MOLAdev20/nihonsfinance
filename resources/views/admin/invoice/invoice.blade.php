@extends('layouts.admin')

@section('title', 'Manajemen Invoice')
@section('page_header', 'Manajemen Invoice')
@section('page_subheader', 'Kelola daftar invoice, lihat detail, edit, atau hapus invoice.')

@section('content')
    <div class="space-y-5" data-invoice-list-page>
        @if (session('successMessage'))
            <div class="hidden" data-invoice-success-message="{{ session('successMessage') }}"></div>
        @endif

        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Daftar Invoice</h2>
                    <p class="text-sm text-slate-500">Klik row untuk membuka detail invoice.</p>
                </div>
                <a
                    class="inline-flex items-center justify-center rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-rose-600"
                    href="{{ route('admin.invoice.create') }}"
                >
                    Tambah Invoice
                </a>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-rose-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-rose-100">
                    <thead class="bg-rose-50/70">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3">Kode Invoice</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Issue Date</th>
                            <th class="px-4 py-3">Due Date</th>
                            <th class="px-4 py-3">Items</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-100 text-sm text-slate-700">
                        @forelse ($invoices as $invoice)
                            <tr
                                class="cursor-pointer transition hover:bg-rose-50/30"
                                data-invoice-row-link="{{ route('admin.invoice.show', $invoice) }}"
                            >
                                <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $invoice->invoice_code }}</td>
                                <td class="px-4 py-3">{{ $invoice->customer?->full_name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $invoice->issue_date?->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $invoice->due_date?->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $invoice->lines_count }}</td>
                                <td class="whitespace-nowrap px-4 py-3">Rp {{ number_format((float) $invoice->total_amount, 2, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-medium uppercase text-rose-700">
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a
                                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                                            href="{{ route('admin.invoice.edit', $invoice) }}"
                                        >
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.invoice.destroy', $invoice) }}" data-invoice-delete-form method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                                type="submit"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="8">
                                    Data invoice belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-rose-100 px-4 py-3">
                {{ $invoices->links() }}
            </div>
        </section>
    </div>
@endsection
