import Bell from "bell-alert";
import "bell-alert/dist/bell.min.css";

export const initProductPage = (shell) => {
    const productPage = shell.querySelector("[data-product-page]");
    if (!productPage) {
        return;
    }

    const productForm = shell.querySelector("[data-product-form]");
    const productTableBody = productPage.querySelector(
        "[data-product-table-body]",
    );
    const modalElement = shell.querySelector("#product-form-modal");
    const modalTrigger = productPage.querySelector(
        "[data-product-modal-trigger]",
    );
    const productIdInput = productForm?.querySelector("[data-product-id]");
    const submitButton = productForm?.querySelector("[data-product-submit]");
    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? "";
    const productEndpoint =
        productPage.getAttribute("data-product-endpoint") ?? "/admin/product";
    const priceFormatter = new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    if (
        !productForm ||
        !productTableBody ||
        !modalElement ||
        !productIdInput ||
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
        return String(value)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    };

    const formatRupiah = (priceValue) => {
        const parsedValue = Number.parseFloat(priceValue ?? 0);
        if (Number.isNaN(parsedValue)) {
            return "Rp 0,00";
        }

        return `Rp ${priceFormatter.format(parsedValue)}`;
    };

    const closeProductModal = () => {
        modalElement.querySelector("[data-modal-close]")?.click();
    };

    const openProductModal = () => {
        modalTrigger?.click();
    };

    const setModalState = (mode) => {
        const modalTitle = modalElement.querySelector("h3");
        if (mode === "edit") {
            modalTitle.textContent = "Edit Produk";
            submitButton.textContent = "Perbarui";
            return;
        }

        modalTitle.textContent = "Tambah Produk";
        submitButton.textContent = "Simpan";
    };

    const clearValidationErrors = () => {
        productForm
            .querySelectorAll("[data-product-error]")
            .forEach((errorElement) => {
                errorElement.textContent = "";
            });

        productForm
            .querySelectorAll("[data-product-input]")
            .forEach((inputElement) => {
                inputElement.classList.remove(
                    "border-red-400",
                    "focus:border-red-400",
                    "focus:ring-red-100",
                );
            });
    };

    const clearFieldError = (fieldName) => {
        const inputElement = productForm.querySelector(
            `[data-product-input="${fieldName}"]`,
        );
        const errorElement = productForm.querySelector(
            `[data-product-error="${fieldName}"]`,
        );

        errorElement.textContent = "";
        inputElement?.classList.remove(
            "border-red-400",
            "focus:border-red-400",
            "focus:ring-red-100",
        );
    };

    const renderEmptyState = () => {
        if (productTableBody.querySelector("[data-product-row-id]")) {
            return;
        }

        productTableBody.innerHTML = `
            <tr data-product-empty-row>
                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="5">
                    Data produk belum tersedia.
                </td>
            </tr>
        `;
    };

    const buildProductRow = (productData) => {
        const safeTitle = escapeHtml(productData.title ?? "");
        const safeDescription = escapeHtml(productData.description ?? "");
        const safeDescriptionText =
            safeDescription.length > 0 ? safeDescription : "-";
        const safePrice = escapeHtml(productData.price ?? "0.00");
        const safeId = Number.parseInt(productData.id, 10);

        return `
            <tr data-product-row-id="${safeId}">
                <td class="whitespace-nowrap px-4 py-3 font-medium">${safeId}</td>
                <td class="px-4 py-3">${safeTitle}</td>
                <td class="whitespace-nowrap px-4 py-3">${formatRupiah(productData.price)}</td>
                <td class="px-4 py-3 text-slate-600">${safeDescriptionText}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                            data-product-action="edit"
                            data-product-description="${safeDescription}"
                            data-product-id="${safeId}"
                            data-product-price="${safePrice}"
                            data-product-title="${safeTitle}"
                            type="button"
                        >
                            Edit
                        </button>
                        <button
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                            data-product-action="delete"
                            data-product-id="${safeId}"
                            type="button"
                        >
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `;
    };

    const upsertProductRow = (productData) => {
        const rowMarkup = buildProductRow(productData);
        const existingRow = productTableBody.querySelector(
            `[data-product-row-id="${productData.id}"]`,
        );

        productTableBody.querySelector("[data-product-empty-row]")?.remove();

        if (existingRow) {
            existingRow.outerHTML = rowMarkup;
            return;
        }

        productTableBody.insertAdjacentHTML("afterbegin", rowMarkup);
    };

    const setCreateMode = () => {
        clearValidationErrors();
        productForm.reset();
        productIdInput.value = "";
        setModalState("create");
    };

    const setEditMode = (buttonElement) => {
        clearValidationErrors();
        setModalState("edit");

        productIdInput.value = buttonElement.dataset.productId ?? "";

        const titleInput = productForm.querySelector(
            '[data-product-input="title"]',
        );
        const priceInput = productForm.querySelector(
            '[data-product-input="price"]',
        );
        const descriptionInput = productForm.querySelector(
            '[data-product-input="description"]',
        );

        titleInput.value = buttonElement.dataset.productTitle ?? "";
        priceInput.value = buttonElement.dataset.productPrice ?? "";
        descriptionInput.value = buttonElement.dataset.productDescription ?? "";
    };

    productForm
        .querySelectorAll("[data-product-input]")
        .forEach((inputElement) => {
            const fieldName = inputElement.getAttribute("data-product-input");
            inputElement.addEventListener("input", () =>
                clearFieldError(fieldName),
            );
        });

    productPage.addEventListener("click", async (event) => {
        const actionButton = event.target.closest("[data-product-action]");
        if (!actionButton) {
            return;
        }

        const actionType = actionButton.getAttribute("data-product-action");

        if (actionType === "create") {
            setCreateMode();
            openProductModal();
            return;
        }

        if (actionType === "edit") {
            setEditMode(actionButton);
            openProductModal();
            return;
        }

        if (actionType !== "delete") {
            return;
        }

        const productId = actionButton.getAttribute("data-product-id");
        const rowElement = actionButton.closest("[data-product-row-id]");

        if (!productId || !rowElement) {
            return;
        }

        const isConfirmed = window.confirm(
            "Apakah Anda yakin ingin menghapus data produk ini?",
        );
        if (!isConfirmed) {
            return;
        }

        try {
            const response = await window.axios.delete(
                `${productEndpoint}/${productId}`,
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
                response.data.message ?? "Data produk berhasil dihapus.",
            );
        } catch {
            window.alert("Terjadi kesalahan saat menghapus data produk.");
        }
    });

    productForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        clearValidationErrors();

        const productId = productIdInput.value;
        const payload = {
            title: productForm
                .querySelector('[data-product-input="title"]')
                .value.trim(),
            price: productForm.querySelector('[data-product-input="price"]')
                .value,
            description: productForm
                .querySelector('[data-product-input="description"]')
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
            if (productId) {
                response = await window.axios.put(
                    `${productEndpoint}/${productId}`,
                    payload,
                    requestConfig,
                );
            } else {
                response = await window.axios.post(
                    productEndpoint,
                    payload,
                    requestConfig,
                );
            }

            upsertProductRow(response.data.product);
            closeProductModal();
            showSuccessAlert(
                response.data.message ?? "Operasi berhasil diproses.",
            );
            setCreateMode();
        } catch (error) {
            if (error.response?.status === 422 && error.response.data?.errors) {
                Object.entries(error.response.data.errors).forEach(
                    ([fieldName, messages]) => {
                        const inputElement = productForm.querySelector(
                            `[data-product-input="${fieldName}"]`,
                        );
                        const errorElement = productForm.querySelector(
                            `[data-product-error="${fieldName}"]`,
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
                window.alert("Terjadi kesalahan saat menyimpan data produk.");
            }
        } finally {
            submitButton.disabled = false;
        }
    });
};
