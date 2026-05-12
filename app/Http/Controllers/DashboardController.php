<?php

namespace App\Http\Controllers;

use App\Models\TransactionModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $currentYear = now()->year;
        $availableYears = $this->getAvailableYears($currentYear);
        $requestedYear = $this->parseRequestedYear($request);
        $selectedYear = $this->resolveSelectedYear($requestedYear, $availableYears, $currentYear);
        $monthlySummary = $this->getMonthlySummary();
        $yearlyChart = $this->getYearlyChartData($selectedYear);

        return view('admin.dashboard', [
            'monthlyExpenseCount' => $monthlySummary['monthlyExpenseCount'],
            'monthlyExpenseAmount' => $monthlySummary['monthlyExpenseAmount'],
            'monthlyIncomeCount' => $monthlySummary['monthlyIncomeCount'],
            'monthlyIncomeAmount' => $monthlySummary['monthlyIncomeAmount'],
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'chartLabels' => $yearlyChart['chartLabels'],
            'expenseChartData' => $yearlyChart['expenseChartData'],
            'incomeChartData' => $yearlyChart['incomeChartData'],
        ]);
    }

    public function chartData(Request $request): JsonResponse
    {
        $currentYear = now()->year;
        $availableYears = $this->getAvailableYears($currentYear);
        $requestedYear = $this->parseRequestedYear($request);
        $selectedYear = $this->resolveSelectedYear($requestedYear, $availableYears, $currentYear);
        $yearlyChart = $this->getYearlyChartData($selectedYear);

        return response()->json([
            'selectedYear' => $selectedYear,
            'chartLabels' => $yearlyChart['chartLabels'],
            'expenseChartData' => $yearlyChart['expenseChartData'],
            'incomeChartData' => $yearlyChart['incomeChartData'],
        ]);
    }

    private function parseRequestedYear(Request $request): ?int
    {
        $year = $request->query('year');
        if ($year === null || $year === '') {
            return null;
        }

        if (!is_numeric($year)) {
            return null;
        }

        $normalizedYear = (int) $year;
        if ($normalizedYear < 2000 || $normalizedYear > 2100) {
            return null;
        }

        return $normalizedYear;
    }

    private function resolveSelectedYear(
        ?int $requestedYear,
        Collection $availableYears,
        int $currentYear
    ): int {
        if ($requestedYear === null) {
            return $currentYear;
        }

        if (!$availableYears->contains($requestedYear)) {
            return $currentYear;
        }

        return $requestedYear;
    }

    private function getAvailableYears(int $currentYear): Collection
    {
        return TransactionModel::query()
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->pluck('year')
            ->map(static fn ($year): int => (int) $year)
            ->push($currentYear)
            ->unique()
            ->sortDesc()
            ->values();
    }

    private function getMonthlySummary(): array
    {
        $now = now();

        $monthlyAggregate = TransactionModel::query()
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->selectRaw("COUNT(CASE WHEN type = 'expense' THEN 1 END) as monthly_expense_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount END), 0) as monthly_expense_amount")
            ->selectRaw("COUNT(CASE WHEN type = 'income' THEN 1 END) as monthly_income_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount END), 0) as monthly_income_amount")
            ->first();

        return [
            'monthlyExpenseCount' => (int) ($monthlyAggregate->monthly_expense_count ?? 0),
            'monthlyExpenseAmount' => (float) ($monthlyAggregate->monthly_expense_amount ?? 0),
            'monthlyIncomeCount' => (int) ($monthlyAggregate->monthly_income_count ?? 0),
            'monthlyIncomeAmount' => (float) ($monthlyAggregate->monthly_income_amount ?? 0),
        ];
    }

    private function getYearlyChartData(int $year): array
    {
        $monthLabels = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        $expenseChartData = array_fill(0, 12, 0.0);
        $incomeChartData = array_fill(0, 12, 0.0);

        $monthlyData = TransactionModel::query()
            ->whereYear('date', $year)
            ->selectRaw('MONTH(date) as month_number')
            ->selectRaw('type')
            ->selectRaw('COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('month_number', 'type')
            ->get();

        foreach ($monthlyData as $row) {
            $monthIndex = ((int) $row->month_number) - 1;
            if ($monthIndex < 0 || $monthIndex > 11) {
                continue;
            }

            if ($row->type === 'expense') {
                $expenseChartData[$monthIndex] = (float) $row->total_amount;
                continue;
            }

            if ($row->type === 'income') {
                $incomeChartData[$monthIndex] = (float) $row->total_amount;
            }
        }

        return [
            'chartLabels' => $monthLabels,
            'expenseChartData' => $expenseChartData,
            'incomeChartData' => $incomeChartData,
        ];
    }
}
