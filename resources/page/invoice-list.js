import Bell from "bell-alert";
import "bell-alert/dist/bell.min.css";

export const initInvoiceListPage = (shell) => {
    const listPage = shell.querySelector("[data-invoice-list-page]");
    if (!listPage) {
        return;
    }

    const successMessageElement = listPage.querySelector(
        "[data-invoice-success-message]",
    );
    const successMessage = successMessageElement?.getAttribute(
        "data-invoice-success-message",
    );

    if (successMessage) {
        const bell = new Bell(
            {
                title: "Berhasil",
                description: successMessage,
            },
            "success",
        );
        bell.launch();
    }

    const errorMessageElement = listPage.querySelector(
        "[data-invoice-error-message]",
    );
    const errorMessage = errorMessageElement?.getAttribute(
        "data-invoice-error-message",
    );

    if (errorMessage) {
        const bell = new Bell(
            {
                title: "Gagal",
                description: errorMessage,
            },
            "error",
        );
        bell.launch();
    }

    listPage.querySelectorAll("[data-invoice-delete-form]").forEach((formElement) => {
        formElement.addEventListener("submit", (event) => {
            const confirmed = window.confirm(
                "Apakah Anda yakin ingin menghapus invoice ini?",
            );
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    listPage.querySelectorAll("[data-invoice-row-link]").forEach((rowElement) => {
        rowElement.addEventListener("click", (event) => {
            const targetElement = event.target;
            if (!(targetElement instanceof Element)) {
                return;
            }

            if (targetElement.closest("a, button, form, input, select, textarea")) {
                return;
            }

            const targetHref = rowElement.getAttribute("data-invoice-row-link");
            if (!targetHref) {
                return;
            }

            window.location.href = targetHref;
        });
    });
};
