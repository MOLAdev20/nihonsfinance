import Bell from "bell-alert";
import "bell-alert/dist/bell.min.css";

export const initTransactionPage = (shell) => {
    const transactionPage = shell.querySelector("[data-transaction-page]");
    if (!transactionPage) {
        return;
    }

    const transactionForm = shell.querySelector("[data-transaction-form]");
    const transactionTableBody = transactionPage.querySelector(
        "[data-transaction-table-body]",
    );
    const modalElement = shell.querySelector("#transaction-form-modal");
    const modalTrigger = transactionPage.querySelector(
        "[data-transaction-modal-trigger]",
    );
    const transactionIdInput =
        transactionForm?.querySelector("[data-transaction-id]");
    const submitButton = transactionForm?.querySelector(
        "[data-transaction-submit]",
    );
    const categoryInput = transactionForm?.querySelector(
        '[data-transaction-input="category_id"]',
    );
    const amountInput = transactionForm?.querySelector(
        '[data-transaction-input="amount"]',
    );
    const dateInput = transactionForm?.querySelector(
        '[data-transaction-input="date"]',
    );
    const typeInput = transactionForm?.querySelector(
        '[data-transaction-input="type"]',
    );
    const typeDisplayInput = transactionForm?.querySelector(
        '[data-transaction-input="type_display"]',
    );
    const descriptionInput = transactionForm?.querySelector(
        '[data-transaction-input="description"]',
    );
    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? "";
    const transactionEndpoint =
        transactionPage.getAttribute("data-transaction-endpoint") ??
        "/admin/transaction";
    const amountFormatter = new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    if (
        !transactionForm ||
        !transactionTableBody ||
        !modalElement ||
        !modalTrigger ||
        !transactionIdInput ||
        !submitButton ||
        !categoryInput ||
        !amountInput ||
        !dateInput ||
        !typeInput ||
        !typeDisplayInput ||
        !descriptionInput
    ) {
        return;
    }

    const typeLabels = {
        income: "Pemasukan",
        expense: "Pengeluaran",
    };

    const showSuccessAlert = (description) => {
        const bell = new Bell(
            {
                title: "Berhasil",
                description,
            },
            "success",
        );
        bell.launch();
    };

    const escapeHtml = (value) => {
        return String(value ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    };

    const getTypeLabel = (typeValue) => {
        return typeLabels[typeValue] ?? "-";
    };

    const getCurrentDateTimeLocal = () => {
        const now = new Date();
        const timezoneOffset = now.getTimezoneOffset() * 60000;
        return new Date(now.getTime() - timezoneOffset).toISOString().slice(0, 16);
    };

    const normalizeDateTimeInput = (value) => {
        if (!value) {
            return getCurrentDateTimeLocal();
        }

        if (value.includes("T")) {
            return value.slice(0, 16);
        }

        return value.replace(" ", "T").slice(0, 16);
    };

    const formatDateTimeDisplay = (value) => {
        const normalizedValue = String(value ?? "").replace(" ", "T");
        const parsedDate = new Date(normalizedValue);
        if (Number.isNaN(parsedDate.getTime())) {
            return "-";
        }

        return parsedDate.toLocaleString("id-ID", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    const formatRupiah = (amountValue) => {
        const parsedValue = Number.parseFloat(amountValue ?? 0);
        if (Number.isNaN(parsedValue)) {
            return "Rp 0,00";
        }

        return `Rp ${amountFormatter.format(parsedValue)}`;
    };

    const closeTransactionModal = () => {
        modalElement.querySelector("[data-modal-close]")?.click();
    };

    const openTransactionModal = () => {
        modalTrigger.click();
    };

    const clearFieldError = (fieldName) => {
        const errorElement = transactionForm.querySelector(
            `[data-transaction-error="${fieldName}"]`,
        );
        const inputElement = transactionForm.querySelector(
            `[data-transaction-input="${fieldName}"]`,
        );

        errorElement.textContent = "";
        inputElement?.classList.remove(
            "border-red-400",
            "focus:border-red-400",
            "focus:ring-red-100",
        );
    };

    const clearValidationErrors = () => {
        transactionForm
            .querySelectorAll("[data-transaction-error]")
            .forEach((errorElement) => {
                errorElement.textContent = "";
            });

        transactionForm
            .querySelectorAll("[data-transaction-input]")
            .forEach((inputElement) => {
                inputElement.classList.remove(
                    "border-red-400",
                    "focus:border-red-400",
                    "focus:ring-red-100",
                );
            });
    };

    const setModalState = (mode) => {
        const modalTitle = modalElement.querySelector("h3");
        if (!modalTitle) {
            return;
        }

        if (mode === "edit") {
            modalTitle.textContent = "Edit Transaksi";
            submitButton.textContent = "Perbarui";
            return;
        }

        modalTitle.textContent = "Tambah Transaksi";
        submitButton.textContent = "Simpan";
    };

    const syncTransactionType = () => {
        const selectedOption = categoryInput.options[categoryInput.selectedIndex];
        const selectedType = selectedOption?.dataset.categoryType ?? "";

        typeInput.value = selectedType;
        typeDisplayInput.value = getTypeLabel(selectedType);
    };

    const renderEmptyState = () => {
        if (transactionTableBody.querySelector("[data-transaction-row-id]")) {
            return;
        }

        transactionTableBody.innerHTML = `
            <tr data-transaction-empty-row>
                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="6">
                    Data transaksi belum tersedia.
                </td>
            </tr>
        `;
    };

    const buildTransactionRow = (transactionData) => {
        const safeId = Number.parseInt(transactionData.id, 10);
        const safeCategoryId = escapeHtml(transactionData.category_id ?? "");
        const safeAmount = escapeHtml(transactionData.amount ?? "0.00");
        const safeType = escapeHtml(transactionData.type ?? "");
        const safeDescription = escapeHtml(transactionData.description ?? "");
        const safeDate = normalizeDateTimeInput(transactionData.date ?? "");
        const categoryTitle = escapeHtml(transactionData.category?.title ?? "-");

        return `
            <tr data-transaction-row-id="${safeId}">
                <td class="px-4 py-3">${categoryTitle}</td>
                <td class="whitespace-nowrap px-4 py-3">${getTypeLabel(transactionData.type)}</td>
                <td class="whitespace-nowrap px-4 py-3">${formatRupiah(transactionData.amount)}</td>
                <td class="whitespace-nowrap px-4 py-3">${formatDateTimeDisplay(transactionData.date)}</td>
                <td class="px-4 py-3 text-slate-600">${safeDescription}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                            data-transaction-action="edit"
                            data-transaction-amount="${safeAmount}"
                            data-transaction-category-id="${safeCategoryId}"
                            data-transaction-date="${safeDate}"
                            data-transaction-description="${safeDescription}"
                            data-transaction-id="${safeId}"
                            data-transaction-type="${safeType}"
                            type="button"
                        >
                            Edit
                        </button>
                        <button
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                            data-transaction-action="delete"
                            data-transaction-id="${safeId}"
                            type="button"
                        >
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `;
    };

    const renderTransactionRows = (transactions) => {
        if (!Array.isArray(transactions) || transactions.length === 0) {
            transactionTableBody.innerHTML = "";
            renderEmptyState();
            return;
        }

        transactionTableBody.innerHTML = transactions
            .map((transactionData) => buildTransactionRow(transactionData))
            .join("");
    };

    const refreshTransactionTable = async () => {
        const response = await window.axios.get(transactionEndpoint, {
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
        });

        renderTransactionRows(response.data.transactions ?? []);
    };

    const setCreateMode = () => {
        clearValidationErrors();
        transactionForm.reset();
        transactionIdInput.value = "";
        dateInput.value = getCurrentDateTimeLocal();
        syncTransactionType();
        setModalState("create");
    };

    const setEditMode = (buttonElement) => {
        clearValidationErrors();
        setModalState("edit");

        transactionIdInput.value = buttonElement.dataset.transactionId ?? "";
        categoryInput.value = buttonElement.dataset.transactionCategoryId ?? "";
        amountInput.value = buttonElement.dataset.transactionAmount ?? "";
        dateInput.value = normalizeDateTimeInput(
            buttonElement.dataset.transactionDate ?? "",
        );
        descriptionInput.value = buttonElement.dataset.transactionDescription ?? "";
        syncTransactionType();
    };

    transactionForm
        .querySelectorAll("[data-transaction-input]")
        .forEach((inputElement) => {
            const fieldName = inputElement.getAttribute("data-transaction-input");
            if (!fieldName) {
                return;
            }

            const clearErrorEventName =
                inputElement.tagName === "SELECT" ? "change" : "input";
            inputElement.addEventListener(clearErrorEventName, () =>
                clearFieldError(fieldName),
            );
        });

    categoryInput.addEventListener("change", () => {
        syncTransactionType();
        clearFieldError("type");
    });

    transactionPage.addEventListener("click", async (event) => {
        const actionButton = event.target.closest("[data-transaction-action]");
        if (!actionButton) {
            return;
        }

        const actionType = actionButton.getAttribute("data-transaction-action");
        if (actionType === "create") {
            setCreateMode();
            openTransactionModal();
            return;
        }

        if (actionType === "edit") {
            setEditMode(actionButton);
            openTransactionModal();
            return;
        }

        if (actionType !== "delete") {
            return;
        }

        const transactionId = actionButton.getAttribute("data-transaction-id");
        const rowElement = actionButton.closest("[data-transaction-row-id]");
        if (!transactionId || !rowElement) {
            return;
        }

        const isConfirmed = window.confirm(
            "Apakah Anda yakin ingin menghapus data transaksi ini?",
        );
        if (!isConfirmed) {
            return;
        }

        try {
            const response = await window.axios.delete(
                `${transactionEndpoint}/${transactionId}`,
                {
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                },
            );

            rowElement.remove();
            renderEmptyState();
            showSuccessAlert(
                response.data.message ?? "Data transaksi berhasil dihapus.",
            );
        } catch {
            window.alert("Terjadi kesalahan saat menghapus data transaksi.");
        }
    });

    transactionForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        clearValidationErrors();

        const transactionId = transactionIdInput.value;
        const payload = {
            category_id: categoryInput.value,
            amount: amountInput.value,
            date: dateInput.value,
            type: typeInput.value,
            description: descriptionInput.value.trim(),
        };

        submitButton.disabled = true;

        try {
            const requestConfig = {
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
            };

            let response;
            if (transactionId) {
                response = await window.axios.put(
                    `${transactionEndpoint}/${transactionId}`,
                    payload,
                    requestConfig,
                );
            } else {
                response = await window.axios.post(
                    transactionEndpoint,
                    payload,
                    requestConfig,
                );
            }

            await refreshTransactionTable();
            closeTransactionModal();
            setCreateMode();
            showSuccessAlert(
                response.data.message ?? "Operasi berhasil diproses.",
            );
        } catch (error) {
            if (error.response?.status === 422 && error.response.data?.errors) {
                Object.entries(error.response.data.errors).forEach(
                    ([fieldName, messages]) => {
                        const inputElement = transactionForm.querySelector(
                            `[data-transaction-input="${fieldName}"]`,
                        );
                        const errorElement = transactionForm.querySelector(
                            `[data-transaction-error="${fieldName}"]`,
                        );

                        if (inputElement) {
                            inputElement.classList.add(
                                "border-red-400",
                                "focus:border-red-400",
                                "focus:ring-red-100",
                            );
                        }

                        if (errorElement) {
                            errorElement.textContent = messages[0] ?? "";
                        }
                    },
                );
            } else {
                window.alert("Terjadi kesalahan saat menyimpan data transaksi.");
            }
        } finally {
            submitButton.disabled = false;
        }
    });
};
