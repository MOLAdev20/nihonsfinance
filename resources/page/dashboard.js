import Chart from "chart.js/auto";

export const initDashboardPage = (shell) => {
    const dashboardPage = shell.querySelector("[data-dashboard-page]");
    if (!dashboardPage) {
        return;
    }

    const yearSelect = dashboardPage.querySelector("[data-dashboard-year-select]");
    const chartElement = dashboardPage.querySelector("[data-dashboard-chart]");
    const chartEndpoint = dashboardPage.getAttribute("data-chart-endpoint") ?? "";

    if (!yearSelect || !chartElement || !chartEndpoint) {
        return;
    }

    const parseJsonArray = (value) => {
        if (!value) {
            return [];
        }

        try {
            const parsedValue = JSON.parse(value);
            return Array.isArray(parsedValue) ? parsedValue : [];
        } catch (error) {
            return [];
        }
    };

    const parseSeries = (series) => {
        return series.map((item) => {
            const parsedItem = Number.parseFloat(item ?? 0);
            return Number.isNaN(parsedItem) ? 0 : parsedItem;
        });
    };

    const createChart = () => {
        const chartLabels = parseJsonArray(
            chartElement.getAttribute("data-chart-labels"),
        );
        const expenseSeries = parseSeries(
            parseJsonArray(chartElement.getAttribute("data-expense-series")),
        );
        const incomeSeries = parseSeries(
            parseJsonArray(chartElement.getAttribute("data-income-series")),
        );

        return new Chart(chartElement, {
            type: "line",
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: "Pengeluaran",
                        data: expenseSeries,
                        borderColor: "#ef4444",
                        backgroundColor: "rgba(239, 68, 68, 0.15)",
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                    {
                        label: "Pemasukan",
                        data: incomeSeries,
                        borderColor: "#16a34a",
                        backgroundColor: "rgba(22, 163, 74, 0.15)",
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: "index",
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: "top",
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const value = Number(context.raw ?? 0);
                                return `${context.dataset.label}: Rp ${value.toLocaleString("id-ID", {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                })}`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => {
                                const normalizedValue = Number(value ?? 0);
                                return `Rp ${normalizedValue.toLocaleString("id-ID")}`;
                            },
                        },
                    },
                },
            },
        });
    };

    const chartInstance = createChart();
    let requestSequence = 0;

    yearSelect.addEventListener("change", async () => {
        requestSequence += 1;
        const currentRequest = requestSequence;

        yearSelect.disabled = true;

        try {
            const response = await window.axios.get(chartEndpoint, {
                params: {
                    year: yearSelect.value,
                },
            });

            if (currentRequest !== requestSequence) {
                return;
            }

            const responseData = response?.data ?? {};
            const updatedLabels = Array.isArray(responseData.chartLabels)
                ? responseData.chartLabels
                : [];
            const updatedExpenseSeries = parseSeries(
                Array.isArray(responseData.expenseChartData)
                    ? responseData.expenseChartData
                    : [],
            );
            const updatedIncomeSeries = parseSeries(
                Array.isArray(responseData.incomeChartData)
                    ? responseData.incomeChartData
                    : [],
            );

            chartInstance.data.labels = updatedLabels;
            chartInstance.data.datasets[0].data = updatedExpenseSeries;
            chartInstance.data.datasets[1].data = updatedIncomeSeries;
            chartInstance.update();

            if (responseData.selectedYear) {
                yearSelect.value = String(responseData.selectedYear);
            }
        } catch (error) {
            console.error("Gagal memperbarui data grafik dashboard.", error);
        } finally {
            if (currentRequest === requestSequence) {
                yearSelect.disabled = false;
            }
        }
    });
};
