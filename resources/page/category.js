import Bell from "bell-alert";
import "bell-alert/dist/bell.min.css";

export const initCategoryPage = (shell) => {
    const categoryPage = shell.querySelector("[data-category-page]");
    if (!categoryPage) {
        return;
    }

    const categoryForm = shell.querySelector("[data-category-form]");
    const categoryTableBody = categoryPage.querySelector(
        "[data-category-table-body]",
    );
    const modalElement = shell.querySelector("#category-form-modal");
    const modalTrigger = categoryPage.querySelector(
        "[data-category-modal-trigger]",
    );
    const categoryIdInput = categoryForm?.querySelector("[data-category-id]");
    const submitButton = categoryForm?.querySelector("[data-category-submit]");
    const titleInput = categoryForm?.querySelector(
        '[data-category-input="title"]',
    );
    const typeInputWrapper = categoryForm?.querySelector(
        "[data-category-type-wrapper]",
    );
    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? "";
    const categoryEndpoint =
        categoryPage.getAttribute("data-category-endpoint") ?? "/admin/category";

    if (
        !categoryForm ||
        !categoryTableBody ||
        !modalElement ||
        !categoryIdInput ||
        !submitButton ||
        !titleInput ||
        !typeInputWrapper
    ) {
        return;
    }

    const categoryTypeLabels = {
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
        return categoryTypeLabels[typeValue] ?? "-";
    };

    const closeCategoryModal = () => {
        modalElement.querySelector("[data-modal-close]")?.click();
    };

    const openCategoryModal = () => {
        modalTrigger?.click();
    };

    const setModalState = (mode) => {
        const modalTitle = modalElement.querySelector("h3");
        if (!modalTitle) {
            return;
        }

        if (mode === "edit") {
            modalTitle.textContent = "Edit Kategori";
            submitButton.textContent = "Perbarui";
            return;
        }

        modalTitle.textContent = "Tambah Kategori";
        submitButton.textContent = "Simpan";
    };

    const clearValidationErrors = () => {
        categoryForm
            .querySelectorAll("[data-category-error]")
            .forEach((errorElement) => {
                errorElement.textContent = "";
            });

        titleInput.classList.remove(
            "border-red-400",
            "focus:border-red-400",
            "focus:ring-red-100",
        );
        typeInputWrapper.classList.remove("border-red-400");
    };

    const clearFieldError = (fieldName) => {
        const errorElement = categoryForm.querySelector(
            `[data-category-error="${fieldName}"]`,
        );
        if (errorElement) {
            errorElement.textContent = "";
        }

        if (fieldName === "title") {
            titleInput.classList.remove(
                "border-red-400",
                "focus:border-red-400",
                "focus:ring-red-100",
            );
            return;
        }

        if (fieldName === "type") {
            typeInputWrapper.classList.remove("border-red-400");
        }
    };

    const showFieldError = (fieldName, message) => {
        const errorElement = categoryForm.querySelector(
            `[data-category-error="${fieldName}"]`,
        );
        if (errorElement) {
            errorElement.textContent = message ?? "";
        }

        if (fieldName === "title") {
            titleInput.classList.add(
                "border-red-400",
                "focus:border-red-400",
                "focus:ring-red-100",
            );
            return;
        }

        if (fieldName === "type") {
            typeInputWrapper.classList.add("border-red-400");
        }
    };

    const renderEmptyState = () => {
        if (categoryTableBody.querySelector("[data-category-row-id]")) {
            return;
        }

        categoryTableBody.innerHTML = `
            <tr data-category-empty-row>
                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="3">
                    Data kategori belum tersedia.
                </td>
            </tr>
        `;
    };

    const buildCategoryRow = (categoryData) => {
        const safeId = Number.parseInt(categoryData.id, 10);
        const safeTitle = escapeHtml(categoryData.title);
        const safeType = escapeHtml(categoryData.type);
        const typeLabel = getTypeLabel(categoryData.type);

        return `
            <tr data-category-row-id="${safeId}">
                <td class="px-4 py-3">${safeTitle}</td>
                <td class="whitespace-nowrap px-4 py-3">${typeLabel}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50"
                            data-category-action="edit"
                            data-category-id="${safeId}"
                            data-category-title="${safeTitle}"
                            data-category-type="${safeType}"
                            type="button"
                        >
                            Edit
                        </button>
                        <button
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                            data-category-action="delete"
                            data-category-id="${safeId}"
                            type="button"
                        >
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `;
    };

    const renderCategoryRows = (categories) => {
        if (!Array.isArray(categories) || categories.length === 0) {
            categoryTableBody.innerHTML = "";
            renderEmptyState();
            return;
        }

        categoryTableBody.innerHTML = categories
            .map((categoryData) => buildCategoryRow(categoryData))
            .join("");
    };

    const refreshCategoryTable = async () => {
        const response = await window.axios.get(categoryEndpoint, {
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
        });

        renderCategoryRows(response.data.categories ?? []);
    };

    const setCreateMode = () => {
        clearValidationErrors();
        categoryForm.reset();
        categoryIdInput.value = "";
        setModalState("create");
    };

    const setEditMode = (buttonElement) => {
        clearValidationErrors();
        setModalState("edit");

        categoryIdInput.value = buttonElement.dataset.categoryId ?? "";
        titleInput.value = buttonElement.dataset.categoryTitle ?? "";

        const targetType = buttonElement.dataset.categoryType ?? "";
        categoryForm
            .querySelectorAll('[data-category-input="type"]')
            .forEach((radioInput) => {
                radioInput.checked = radioInput.value === targetType;
            });
    };

    titleInput.addEventListener("input", () => clearFieldError("title"));
    categoryForm
        .querySelectorAll('[data-category-input="type"]')
        .forEach((radioInput) => {
            radioInput.addEventListener("change", () => clearFieldError("type"));
        });

    categoryPage.addEventListener("click", async (event) => {
        const actionButton = event.target.closest("[data-category-action]");
        if (!actionButton) {
            return;
        }

        const actionType = actionButton.getAttribute("data-category-action");
        if (actionType === "create") {
            setCreateMode();
            openCategoryModal();
            return;
        }

        if (actionType === "edit") {
            setEditMode(actionButton);
            openCategoryModal();
            return;
        }

        if (actionType !== "delete") {
            return;
        }

        const categoryId = actionButton.getAttribute("data-category-id");
        const rowElement = actionButton.closest("[data-category-row-id]");
        if (!categoryId || !rowElement) {
            return;
        }

        const isConfirmed = window.confirm(
            "Apakah Anda yakin ingin menghapus data kategori ini?",
        );
        if (!isConfirmed) {
            return;
        }

        try {
            const response = await window.axios.delete(
                `${categoryEndpoint}/${categoryId}`,
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
                response.data.message ?? "Data kategori berhasil dihapus.",
            );
        } catch {
            window.alert("Terjadi kesalahan saat menghapus data kategori.");
        }
    });

    categoryForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        clearValidationErrors();

        const selectedType = categoryForm.querySelector(
            '[data-category-input="type"]:checked',
        );

        const categoryId = categoryIdInput.value;
        const payload = {
            title: titleInput.value.trim(),
            type: selectedType?.value ?? "",
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
            if (categoryId) {
                response = await window.axios.put(
                    `${categoryEndpoint}/${categoryId}`,
                    payload,
                    requestConfig,
                );
            } else {
                response = await window.axios.post(
                    categoryEndpoint,
                    payload,
                    requestConfig,
                );
            }

            await refreshCategoryTable();
            closeCategoryModal();
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
                window.alert("Terjadi kesalahan saat menyimpan data kategori.");
            }
        } finally {
            submitButton.disabled = false;
        }
    });
};
