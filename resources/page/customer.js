import Bell from "bell-alert";
import "bell-alert/dist/bell.min.css";

export const initCustomerPage = (shell) => {
    const customerPage = shell.querySelector("[data-customer-page]");
    if (!customerPage) {
        return;
    }

    const customerForm = shell.querySelector("[data-customer-form]");
    const customerTableBody = customerPage.querySelector(
        "[data-customer-table-body]",
    );
    const modalElement = shell.querySelector("#customer-form-modal");
    const modalTrigger = customerPage.querySelector(
        "[data-customer-modal-trigger]",
    );
    const customerIdInput = customerForm?.querySelector("[data-customer-id]");
    const submitButton = customerForm?.querySelector("[data-customer-submit]");
    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? "";
    const customerEndpoint =
        customerPage.getAttribute("data-customer-endpoint") ?? "/admin/customer";

    if (
        !customerForm ||
        !customerTableBody ||
        !modalElement ||
        !customerIdInput ||
        !submitButton
    ) {
        return;
    }

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

    const closeCustomerModal = () => {
        modalElement.querySelector("[data-modal-close]")?.click();
    };

    const openCustomerModal = () => {
        modalTrigger?.click();
    };

    const setModalState = (mode) => {
        const modalTitle = modalElement.querySelector("h3");
        if (!modalTitle) {
            return;
        }

        if (mode === "edit") {
            modalTitle.textContent = "Edit Customer";
            submitButton.textContent = "Perbarui";
            return;
        }

        modalTitle.textContent = "Tambah Customer";
        submitButton.textContent = "Simpan";
    };

    const normalizeFieldName = (fieldName) => {
        if (fieldName === "full_name") {
            return "fullname";
        }

        return fieldName;
    };

    const clearValidationErrors = () => {
        customerForm
            .querySelectorAll("[data-customer-error]")
            .forEach((errorElement) => {
                errorElement.textContent = "";
            });

        customerForm
            .querySelectorAll("[data-customer-input]")
            .forEach((inputElement) => {
                inputElement.classList.remove(
                    "border-red-400",
                    "focus:border-red-400",
                    "focus:ring-red-100",
                );
            });
    };

    const clearFieldError = (fieldName) => {
        const normalizedFieldName = normalizeFieldName(fieldName);
        const inputElement = customerForm.querySelector(
            `[data-customer-input="${normalizedFieldName}"]`,
        );
        const errorElement = customerForm.querySelector(
            `[data-customer-error="${normalizedFieldName}"]`,
        );

        if (errorElement) {
            errorElement.textContent = "";
        }

        inputElement?.classList.remove(
            "border-red-400",
            "focus:border-red-400",
            "focus:ring-red-100",
        );
    };

    const showFieldError = (fieldName, message) => {
        const normalizedFieldName = normalizeFieldName(fieldName);
        const inputElement = customerForm.querySelector(
            `[data-customer-input="${normalizedFieldName}"]`,
        );
        const errorElement = customerForm.querySelector(
            `[data-customer-error="${normalizedFieldName}"]`,
        );

        if (errorElement) {
            errorElement.textContent = message ?? "";
        }

        inputElement?.classList.add(
            "border-red-400",
            "focus:border-red-400",
            "focus:ring-red-100",
        );
    };

    const renderEmptyState = () => {
        if (customerTableBody.querySelector("[data-customer-row-id]")) {
            return;
        }

        customerTableBody.innerHTML = `
            <tr data-customer-empty-row>
                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="4">
                    Data customer belum tersedia.
                </td>
            </tr>
        `;
    };

    const buildCustomerRow = (customerData) => {
        const safeId = Number.parseInt(customerData.id, 10);
        const safeFullName = escapeHtml(
            customerData.fullname ?? customerData.full_name,
        );
        const safeEmail = escapeHtml(customerData.email ?? "");
        const safeAddress = escapeHtml(customerData.address ?? "");
        const safeAddressText = safeAddress.length > 0 ? safeAddress : "-";

        return `
            <tr data-customer-row-id="${safeId}">
                <td class="px-4 py-3">${safeFullName}</td>
                <td class="px-4 py-3">${safeEmail}</td>
                <td class="px-4 py-3 text-slate-600">${safeAddressText}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                            data-customer-action="edit"
                            data-customer-address="${safeAddress}"
                            data-customer-email="${safeEmail}"
                            data-customer-fullname="${safeFullName}"
                            data-customer-id="${safeId}"
                            type="button"
                        >
                            Edit
                        </button>
                        <button
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                            data-customer-action="delete"
                            data-customer-id="${safeId}"
                            type="button"
                        >
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `;
    };

    const upsertCustomerRow = (customerData) => {
        const rowMarkup = buildCustomerRow(customerData);
        const existingRow = customerTableBody.querySelector(
            `[data-customer-row-id="${customerData.id}"]`,
        );

        customerTableBody.querySelector("[data-customer-empty-row]")?.remove();

        if (existingRow) {
            existingRow.outerHTML = rowMarkup;
            return;
        }

        customerTableBody.insertAdjacentHTML("afterbegin", rowMarkup);
    };

    const setCreateMode = () => {
        clearValidationErrors();
        customerForm.reset();
        customerIdInput.value = "";
        setModalState("create");
    };

    const setEditMode = (buttonElement) => {
        clearValidationErrors();
        setModalState("edit");

        customerIdInput.value = buttonElement.dataset.customerId ?? "";
        customerForm.querySelector('[data-customer-input="fullname"]').value =
            buttonElement.dataset.customerFullname ?? "";
        customerForm.querySelector('[data-customer-input="email"]').value =
            buttonElement.dataset.customerEmail ?? "";
        customerForm.querySelector('[data-customer-input="address"]').value =
            buttonElement.dataset.customerAddress ?? "";
    };

    customerForm
        .querySelectorAll("[data-customer-input]")
        .forEach((inputElement) => {
            const fieldName = inputElement.getAttribute("data-customer-input");
            inputElement.addEventListener("input", () => clearFieldError(fieldName));
        });

    customerPage.addEventListener("click", async (event) => {
        const actionButton = event.target.closest("[data-customer-action]");
        if (!actionButton) {
            return;
        }

        const actionType = actionButton.getAttribute("data-customer-action");
        if (actionType === "create") {
            setCreateMode();
            openCustomerModal();
            return;
        }

        if (actionType === "edit") {
            setEditMode(actionButton);
            openCustomerModal();
            return;
        }

        if (actionType !== "delete") {
            return;
        }

        const customerId = actionButton.getAttribute("data-customer-id");
        const rowElement = actionButton.closest("[data-customer-row-id]");
        if (!customerId || !rowElement) {
            return;
        }

        const isConfirmed = window.confirm(
            "Apakah Anda yakin ingin menghapus data customer ini?",
        );
        if (!isConfirmed) {
            return;
        }

        try {
            const response = await window.axios.delete(
                `${customerEndpoint}/${customerId}`,
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
                response.data.message ?? "Data customer berhasil dihapus.",
            );
        } catch {
            window.alert("Terjadi kesalahan saat menghapus data customer.");
        }
    });

    customerForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        clearValidationErrors();

        const customerId = customerIdInput.value;
        const payload = {
            fullname: customerForm
                .querySelector('[data-customer-input="fullname"]')
                .value.trim(),
            email: customerForm.querySelector('[data-customer-input="email"]')
                .value.trim(),
            address: customerForm
                .querySelector('[data-customer-input="address"]')
                .value.trim(),
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
            if (customerId) {
                response = await window.axios.put(
                    `${customerEndpoint}/${customerId}`,
                    payload,
                    requestConfig,
                );
            } else {
                response = await window.axios.post(
                    customerEndpoint,
                    payload,
                    requestConfig,
                );
            }

            upsertCustomerRow(response.data.customer ?? {});
            closeCustomerModal();
            setCreateMode();
            showSuccessAlert(
                response.data.message ?? "Operasi berhasil diproses.",
            );
        } catch (error) {
            if (error.response?.status === 422 && error.response.data?.errors) {
                Object.entries(error.response.data.errors).forEach(
                    ([fieldName, messages]) => {
                        showFieldError(fieldName, messages[0] ?? "");
                    },
                );
            } else {
                window.alert("Terjadi kesalahan saat menyimpan data customer.");
            }
        } finally {
            submitButton.disabled = false;
        }
    });
};
