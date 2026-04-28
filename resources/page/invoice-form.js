const escapeHtml = (value) => {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
};

const toNumber = (value) => {
    const parsedValue = Number.parseFloat(value ?? "0");
    if (Number.isNaN(parsedValue)) {
        return 0;
    }

    return parsedValue;
};

const normalizeString = (value) => {
    return String(value ?? "").trim().toLowerCase();
};

export const initInvoiceFormPage = (shell) => {
    const invoicePage = shell.querySelector("[data-invoice-form-page]");
    if (!invoicePage) {
        return;
    }

    const invoiceForm = invoicePage.querySelector("[data-invoice-form]");
    const issueDateInput = invoicePage.querySelector("[data-invoice-issue-date]");
    const dueDateInput = invoicePage.querySelector("[data-invoice-due-date]");
    const customerTriggerWrap = invoicePage.querySelector(
        "[data-customer-trigger-wrap]",
    );
    const customerPickerWrap = invoicePage.querySelector(
        "[data-customer-picker-wrap]",
    );
    const customerDetailWrap = invoicePage.querySelector(
        "[data-customer-detail-wrap]",
    );
    const customerIdInput = invoicePage.querySelector("[data-invoice-customer-id]");
    const customerTriggerButton = invoicePage.querySelector(
        "[data-invoice-customer-trigger]",
    );
    const customerSelectedLabel = invoicePage.querySelector(
        "[data-invoice-customer-selected-label]",
    );
    const customerDropdown = invoicePage.querySelector(
        "[data-invoice-customer-dropdown]",
    );
    const customerSearchInput = invoicePage.querySelector(
        "[data-invoice-customer-search]",
    );
    const customerOptionCountElement = invoicePage.querySelector(
        "[data-invoice-customer-option-count]",
    );
    const customerOptionsList = invoicePage.querySelector(
        "[data-invoice-customer-options]",
    );
    const customerDetailName = invoicePage.querySelector("[data-customer-detail-name]");
    const customerDetailEmail = invoicePage.querySelector(
        "[data-customer-detail-email]",
    );
    const customerDetailAddress = invoicePage.querySelector(
        "[data-customer-detail-address]",
    );
    const addNewCustomerLink = invoicePage.querySelector(
        '[data-invoice-action="open-add-customer-modal"]',
    );
    const closeCustomerPickerButton = invoicePage.querySelector(
        '[data-invoice-action="close-customer-picker"]',
    );
    const editCustomerSelectionButton = invoicePage.querySelector(
        '[data-invoice-action="edit-customer-selection"]',
    );
    const openCustomerModalTrigger = invoicePage.querySelector(
        "[data-invoice-open-customer-modal]",
    );
    const addProductButton = invoicePage.querySelector("[data-invoice-add-product]");
    const invoiceItemsBody = invoicePage.querySelector("[data-invoice-items-body]");
    const totalPaymentElement = invoicePage.querySelector(
        "[data-invoice-total-payment]",
    );
    const amountDueElement = invoicePage.querySelector("[data-invoice-amount-due]");
    const addCustomerForm = shell.querySelector("[data-invoice-add-customer-form]");
    const addCustomerModal = shell.querySelector("#add-customer-modal");

    if (
        !invoiceForm ||
        !customerTriggerWrap ||
        !customerPickerWrap ||
        !customerDetailWrap ||
        !customerIdInput ||
        !customerTriggerButton ||
        !customerSelectedLabel ||
        !customerDropdown ||
        !customerSearchInput ||
        !customerOptionCountElement ||
        !customerOptionsList ||
        !customerDetailName ||
        !customerDetailEmail ||
        !customerDetailAddress ||
        !addNewCustomerLink ||
        !closeCustomerPickerButton ||
        !editCustomerSelectionButton ||
        !openCustomerModalTrigger ||
        !addProductButton ||
        !invoiceItemsBody ||
        !totalPaymentElement ||
        !amountDueElement ||
        !addCustomerForm
    ) {
        return;
    }

    const customerDataElement = invoicePage.querySelector("[data-invoice-customers]");
    const productDataElement = invoicePage.querySelector("[data-invoice-products]");
    const selectedCustomerDataElement = invoicePage.querySelector(
        "[data-invoice-selected-customer]",
    );
    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? "";
    const customerStoreEndpoint =
        invoiceForm.getAttribute("data-customer-store-endpoint") ??
        "/admin/invoice/customer";
    const currencyFormatter = new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const parseDataCollection = (scriptElement) => {
        try {
            return JSON.parse(scriptElement?.textContent ?? "[]");
        } catch (error) {
            console.error("Data invoice tidak valid.", error);
            return [];
        }
    };

    const formatCurrency = (amountValue) => {
        return `Rp ${currencyFormatter.format(toNumber(amountValue))}`;
    };

    const toDateInputValue = (dateValue) => {
        const timezoneOffset = dateValue.getTimezoneOffset() * 60000;
        return new Date(dateValue.getTime() - timezoneOffset)
            .toISOString()
            .slice(0, 10);
    };

    const setDefaultDates = () => {
        const today = new Date();
        const nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);

        if (issueDateInput && !issueDateInput.value) {
            issueDateInput.value = toDateInputValue(today);
        }

        if (dueDateInput && !dueDateInput.value) {
            dueDateInput.value = toDateInputValue(nextWeek);
        }
    };

    let customers = parseDataCollection(customerDataElement).map((customer) => {
        return {
            id: String(customer.id),
            full_name: String(customer.full_name ?? ""),
            email: String(customer.email ?? ""),
            address: String(customer.address ?? ""),
        };
    });

    const products = parseDataCollection(productDataElement).map((product) => {
        return {
            id: String(product.id),
            title: String(product.title ?? ""),
            price: toNumber(product.price),
        };
    });

    const selectedCustomerData = parseDataCollection(selectedCustomerDataElement);
    let selectedCustomer = null;

    const existingItemIndexes = Array.from(
        invoiceItemsBody.querySelectorAll("[data-invoice-item-row] [data-invoice-item-product]"),
    )
        .map((selectElement) => {
            const inputName = selectElement.getAttribute("name") ?? "";
            const nameMatch = inputName.match(/^items\[(\d+)\]\[product_id\]$/);
            return nameMatch ? Number.parseInt(nameMatch[1], 10) : -1;
        })
        .filter((value) => Number.isFinite(value) && value >= 0);
    let itemRowCount = Math.max(-1, ...existingItemIndexes);

    const setCustomerState = (state) => {
        customerTriggerWrap.classList.toggle("hidden", state !== "button");
        customerPickerWrap.classList.toggle("hidden", state !== "picker");
        customerDetailWrap.classList.toggle("hidden", state !== "detail");
    };

    const openCustomerDropdown = () => {
        customerDropdown.classList.remove("hidden");
        customerTriggerButton.setAttribute("aria-expanded", "true");
        customerSearchInput.focus();
    };

    const closeCustomerDropdown = () => {
        customerDropdown.classList.add("hidden");
        customerTriggerButton.setAttribute("aria-expanded", "false");
    };

    const showAddCustomerButton = () => {
        closeCustomerDropdown();
        setCustomerState("button");
    };

    const showCustomerPicker = ({ resetSearch = false } = {}) => {
        setCustomerState("picker");
        if (resetSearch) {
            customerSearchInput.value = "";
        }
        renderCustomerOptions(customerSearchInput.value);
        openCustomerDropdown();
    };

    const showCustomerDetail = (customer) => {
        selectedCustomer = customer;
        customerIdInput.value = customer.id;
        customerSelectedLabel.textContent = customer.full_name;
        customerDetailName.textContent = customer.full_name;
        customerDetailEmail.textContent = customer.email || "-";
        customerDetailAddress.textContent = customer.address || "-";
        closeCustomerDropdown();
        setCustomerState("detail");
    };

    const renderCustomerOptions = (queryValue = "") => {
        const normalizedQuery = normalizeString(queryValue);
        const filteredCustomers = customers.filter((customer) => {
            if (!normalizedQuery) {
                return true;
            }

            return normalizeString(customer.full_name).includes(normalizedQuery);
        });

        customerOptionCountElement.textContent = `${filteredCustomers.length} customer ditemukan`;

        if (!filteredCustomers.length) {
            customerOptionsList.innerHTML = `
                <li class="px-3 py-2 text-xs text-slate-500">
                    Customer tidak ditemukan.
                </li>
            `;
        } else {
            customerOptionsList.innerHTML = filteredCustomers
                .map((customer) => {
                    return `
                        <li>
                            <button
                                class="w-full px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-rose-50"
                                data-customer-address="${escapeHtml(customer.address)}"
                                data-customer-email="${escapeHtml(customer.email)}"
                                data-customer-id="${escapeHtml(customer.id)}"
                                data-customer-name="${escapeHtml(customer.full_name)}"
                                data-invoice-customer-option
                                type="button"
                            >
                                ${escapeHtml(customer.full_name)}
                            </button>
                        </li>
                    `;
                })
                .join("");
        }

        const shouldShowAddNewLink =
            normalizedQuery.length > 0 && filteredCustomers.length === 0;
        addNewCustomerLink.classList.toggle("hidden", !shouldShowAddNewLink);
    };

    const ensureEmptyState = () => {
        const hasRows = invoiceItemsBody.querySelector("[data-invoice-item-row]");
        if (hasRows) {
            return;
        }

        invoiceItemsBody.innerHTML = `
            <tr data-invoice-empty-row>
                <td class="px-4 py-8 text-center text-sm text-slate-500" colspan="5">
                    Belum ada item invoice. Klik Add Product untuk menambahkan baris pertama.
                </td>
            </tr>
        `;
    };

    const updateInvoiceSummary = () => {
        const rowElements = Array.from(
            invoiceItemsBody.querySelectorAll("[data-invoice-item-row]"),
        );

        const totalAmount = rowElements.reduce((carry, rowElement) => {
            return carry + toNumber(rowElement.dataset.subtotal);
        }, 0);

        totalPaymentElement.textContent = formatCurrency(totalAmount);
        amountDueElement.textContent = formatCurrency(totalAmount);
    };

    const getProductOptionMarkup = () => {
        if (!products.length) {
            return '<option value="">Produk belum tersedia</option>';
        }

        return [
            '<option value="">Pilih item</option>',
            ...products.map((product) => {
                return `<option data-product-price="${escapeHtml(product.price)}" value="${escapeHtml(product.id)}">${escapeHtml(product.title)}</option>`;
            }),
        ].join("");
    };

    const syncRowAmount = (rowElement) => {
        const productSelect = rowElement.querySelector(
            "[data-invoice-item-product]",
        );
        const quantityInput = rowElement.querySelector("[data-invoice-item-qty]");
        const unitPriceElement = rowElement.querySelector(
            "[data-invoice-item-unit-price]",
        );
        const totalElement = rowElement.querySelector("[data-invoice-item-total]");

        if (!productSelect || !quantityInput || !unitPriceElement || !totalElement) {
            return;
        }

        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const unitPrice = toNumber(selectedOption?.dataset.productPrice ?? 0);
        const quantity = Math.max(0, toNumber(quantityInput.value));
        const rowTotal = unitPrice * quantity;

        unitPriceElement.textContent = formatCurrency(unitPrice);
        totalElement.textContent = formatCurrency(rowTotal);
        rowElement.dataset.subtotal = String(rowTotal);

        updateInvoiceSummary();
    };

    const addItemRow = () => {
        invoiceItemsBody.querySelector("[data-invoice-empty-row]")?.remove();
        itemRowCount += 1;

        const rowIdentifier = itemRowCount;
        invoiceItemsBody.insertAdjacentHTML(
            "beforeend",
            `
                <tr data-invoice-item-row data-subtotal="0">
                    <td class="px-4 py-3">
                        <select
                            class="w-full rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                            data-invoice-item-product
                            name="items[${rowIdentifier}][product_id]"
                        >
                            ${getProductOptionMarkup()}
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <input
                            class="w-28 rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                            data-invoice-item-qty
                            min="1"
                            name="items[${rowIdentifier}][qty]"
                            type="number"
                            value="1"
                        >
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
            `,
        );

        const insertedRow = invoiceItemsBody.lastElementChild;
        if (!insertedRow) {
            return;
        }

        attachItemRowListeners(insertedRow);

        syncRowAmount(insertedRow);
    };

    const attachItemRowListeners = (rowElement) => {
        rowElement
            .querySelector("[data-invoice-item-product]")
            ?.addEventListener("change", () => {
                syncRowAmount(rowElement);
            });

        rowElement
            .querySelector("[data-invoice-item-qty]")
            ?.addEventListener("input", (event) => {
                const inputElement = event.currentTarget;
                if (!(inputElement instanceof HTMLInputElement)) {
                    return;
                }

                if (!inputElement.value || toNumber(inputElement.value) < 1) {
                    inputElement.value = "1";
                }

                syncRowAmount(rowElement);
            });

        rowElement
            .querySelector("[data-invoice-item-remove]")
            ?.addEventListener("click", () => {
                rowElement.remove();
                ensureEmptyState();

                if (!invoiceItemsBody.querySelector("[data-invoice-item-row]")) {
                    addItemRow();
                }

                updateInvoiceSummary();
            });
    };

    const closeAddCustomerModal = () => {
        addCustomerModal?.querySelector("[data-modal-close]")?.click();
    };

    const clearCustomerFormError = () => {
        addCustomerForm
            .querySelectorAll("[data-add-customer-error]")
            .forEach((errorElement) => {
                errorElement.textContent = "";
            });

        addCustomerForm
            .querySelectorAll("[data-add-customer-input]")
            .forEach((inputElement) => {
                inputElement.classList.remove(
                    "border-red-400",
                    "focus:border-red-400",
                    "focus:ring-red-100",
                );
            });
    };

    const setCustomerFieldError = (fieldName, message) => {
        const inputElement = addCustomerForm.querySelector(
            `[data-add-customer-input="${fieldName}"]`,
        );
        const errorElement = addCustomerForm.querySelector(
            `[data-add-customer-error="${fieldName}"]`,
        );

        inputElement?.classList.add(
            "border-red-400",
            "focus:border-red-400",
            "focus:ring-red-100",
        );
        if (errorElement instanceof HTMLElement) {
            errorElement.textContent = message;
        }
    };

    invoicePage
        .querySelector('[data-invoice-action="activate-customer-picker"]')
        ?.addEventListener("click", () => {
            showCustomerPicker({ resetSearch: true });
        });

    closeCustomerPickerButton.addEventListener("click", () => {
        if (selectedCustomer) {
            showCustomerDetail(selectedCustomer);
            return;
        }

        showAddCustomerButton();
    });

    editCustomerSelectionButton.addEventListener("click", () => {
        showCustomerPicker({ resetSearch: false });
    });

    customerTriggerButton.addEventListener("click", () => {
        if (customerDropdown.classList.contains("hidden")) {
            openCustomerDropdown();
            return;
        }

        closeCustomerDropdown();
    });

    customerSearchInput.addEventListener("input", (event) => {
        const inputElement = event.currentTarget;
        if (!(inputElement instanceof HTMLInputElement)) {
            return;
        }

        renderCustomerOptions(inputElement.value);
    });

    customerOptionsList.addEventListener("click", (event) => {
        const optionButton = event.target.closest("[data-invoice-customer-option]");
        if (!(optionButton instanceof HTMLButtonElement)) {
            return;
        }

        const customerId = optionButton.dataset.customerId ?? "";
        const customerName = optionButton.dataset.customerName ?? "";
        if (!customerId || !customerName) {
            return;
        }

        showCustomerDetail({
            id: customerId,
            full_name: customerName,
            email: optionButton.dataset.customerEmail ?? "",
            address: optionButton.dataset.customerAddress ?? "",
        });
    });

    document.addEventListener("click", (event) => {
        if (!customerPickerWrap.contains(event.target)) {
            closeCustomerDropdown();
        }
    });

    addNewCustomerLink.addEventListener("click", (event) => {
        event.preventDefault();
        openCustomerModalTrigger.click();
    });

    addCustomerForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        clearCustomerFormError();

        const fullNameInput = addCustomerForm.querySelector(
            '[data-add-customer-input="full_name"]',
        );
        const emailInput = addCustomerForm.querySelector(
            '[data-add-customer-input="email"]',
        );
        const addressInput = addCustomerForm.querySelector(
            '[data-add-customer-input="address"]',
        );

        if (
            !(fullNameInput instanceof HTMLInputElement) ||
            !(emailInput instanceof HTMLInputElement) ||
            !(addressInput instanceof HTMLTextAreaElement)
        ) {
            return;
        }

        const payload = {
            full_name: fullNameInput.value.trim(),
            email: emailInput.value.trim(),
            address: addressInput.value.trim(),
        };

        try {
            const response = await fetch(customerStoreEndpoint, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const responseData = await response.json();

            if (!response.ok) {
                if (response.status === 422 && responseData.errors) {
                    Object.entries(responseData.errors).forEach(
                        ([fieldName, messages]) => {
                            const message = Array.isArray(messages)
                                ? (messages[0] ?? "Input tidak valid.")
                                : "Input tidak valid.";
                            setCustomerFieldError(fieldName, message);
                        },
                    );
                    return;
                }

                throw new Error("Gagal menyimpan customer.");
            }

            const createdCustomer = {
                id: String(responseData.customer?.id ?? ""),
                full_name: String(responseData.customer?.full_name ?? ""),
                email: String(responseData.customer?.email ?? ""),
                address: String(responseData.customer?.address ?? ""),
            };

            if (!createdCustomer.id || !createdCustomer.full_name) {
                throw new Error("Data customer tidak lengkap.");
            }

            customers = [...customers, createdCustomer];
            renderCustomerOptions(customerSearchInput.value);
            showCustomerDetail(createdCustomer);
            addCustomerForm.reset();
            closeAddCustomerModal();
        } catch (error) {
            setCustomerFieldError("full_name", "Gagal menyimpan customer.");
        }
    });

    addProductButton.addEventListener("click", () => {
        addItemRow();
    });

    invoiceForm.addEventListener("submit", () => {
        invoiceItemsBody
            .querySelectorAll("[data-invoice-item-row]")
            .forEach((rowElement) => {
                syncRowAmount(rowElement);
            });
    });

    setDefaultDates();
    renderCustomerOptions();

    invoiceItemsBody.querySelectorAll("[data-invoice-item-row]").forEach((rowElement) => {
        attachItemRowListeners(rowElement);
        syncRowAmount(rowElement);
    });

    if (
        selectedCustomerData &&
        typeof selectedCustomerData === "object" &&
        selectedCustomerData.id
    ) {
        showCustomerDetail({
            id: String(selectedCustomerData.id),
            full_name: String(selectedCustomerData.full_name ?? ""),
            email: String(selectedCustomerData.email ?? ""),
            address: String(selectedCustomerData.address ?? ""),
        });
    } else {
        showAddCustomerButton();
    }

    updateInvoiceSummary();
};
