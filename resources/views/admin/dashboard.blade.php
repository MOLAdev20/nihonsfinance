@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_header', 'Dashboard Admin')
@section('page_subheader', 'Ringkasan perjalanan keuangan bulanan dan pertumbuhan tahunan.')

@section('content')
    <div
        class="space-y-5"
        data-dashboard-page
        data-chart-endpoint="{{ route('admin.dashboard.chartData') }}"
    >
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-rose-100 bg-rose-50/60 p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Pengeluaran Bulan Ini</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">{{ number_format((int) $monthlyExpenseCount, 0, ',', '.') }}</p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jumlah Pengeluaran Bulan Ini</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">
                    Rp {{ number_format((float) $monthlyExpenseAmount, 2, ',', '.') }}
                </p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Pemasukan Bulan Ini</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">{{ number_format((int) $monthlyIncomeCount, 0, ',', '.') }}</p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Jumlah Pemasukan Bulan Ini</p>
                <p class="mt-2 text-2xl font-semibold text-slate-800">
                    Rp {{ number_format((float) $monthlyIncomeAmount, 2, ',', '.') }}
                </p>
            </article>
        </section>

        <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Grafik Pertumbuhan Keuangan Tahunan</h2>
                    <p class="text-sm text-slate-500">
                        Pantau tren nominal pengeluaran dan pemasukan per bulan untuk tahun yang dipilih.
                    </p>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <span>Tahun</span>
                    <select
                        class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                        data-dashboard-year-select
                    >
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" {{ (int) $selectedYear === (int) $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mt-5 h-80">
                <canvas
                    data-dashboard-chart
                    data-chart-labels='@json($chartLabels)'
                    data-expense-series='@json($expenseChartData)'
                    data-income-series='@json($incomeChartData)'
                ></canvas>
            </div>
        </section>
    </div>

@endsection
