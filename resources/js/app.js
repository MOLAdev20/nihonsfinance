import "./bootstrap";

const loadCustomPageScripts = async (shell) => {
    const pageLoaders = [
        {
            selector: "[data-product-page]",
            loader: () => import("../page/product.js"),
            exportName: "initProductPage",
        },
        {
            selector: "[data-category-page]",
            loader: () => import("../page/category.js"),
            exportName: "initCategoryPage",
        },
        {
            selector: "[data-transaction-page]",
            loader: () => import("../page/transaction.js"),
            exportName: "initTransactionPage",
        },
    ];

    for (const pageLoader of pageLoaders) {
        if (!shell.querySelector(pageLoader.selector)) {
            continue;
        }

        try {
            const pageModule = await pageLoader.loader();
            const initPage = pageModule[pageLoader.exportName];
            if (typeof initPage === "function") {
                initPage(shell);
            }
        } catch (error) {
            console.error("Gagal memuat script halaman.", error);
        }
    }
};

const onReady = () => {
    const shell = document.querySelector("[data-admin-shell]");
    if (!shell) {
        return;
    }

    const sidebar = shell.querySelector("[data-sidebar]");
    const overlay = shell.querySelector("[data-sidebar-overlay]");
    const mobileBreakpoint = window.matchMedia("(max-width: 1023px)");

    const closeSidebar = () => {
        if (!sidebar || !overlay) {
            return;
        }

        sidebar.classList.add("-translate-x-full");
        overlay.classList.add("pointer-events-none", "opacity-0");
        shell.querySelectorAll("[data-sidebar-toggle]").forEach((toggle) => {
            toggle.setAttribute("aria-expanded", "false");
        });
    };

    const openSidebar = () => {
        if (!sidebar || !overlay) {
            return;
        }

        sidebar.classList.remove("-translate-x-full");
        overlay.classList.remove("pointer-events-none", "opacity-0");
        shell.querySelectorAll("[data-sidebar-toggle]").forEach((toggle) => {
            toggle.setAttribute("aria-expanded", "true");
        });
    };

    shell.querySelectorAll("[data-sidebar-toggle]").forEach((toggle) => {
        toggle.addEventListener("click", () => {
            if (!mobileBreakpoint.matches) {
                return;
            }

            const isClosed = sidebar?.classList.contains("-translate-x-full");
            if (isClosed) {
                openSidebar();
                return;
            }

            closeSidebar();
        });
    });

    shell.querySelectorAll("[data-sidebar-close]").forEach((toggle) => {
        toggle.addEventListener("click", closeSidebar);
    });

    overlay?.addEventListener("click", closeSidebar);

    window.addEventListener("resize", () => {
        if (!mobileBreakpoint.matches) {
            closeSidebar();
        }
    });

    const setSubmenuState = (toggle, submenu, expanded) => {
        toggle.setAttribute("aria-expanded", expanded ? "true" : "false");

        const chevron = toggle.querySelector("[data-submenu-chevron]");
        if (expanded) {
            submenu.classList.remove("opacity-0", "max-h-0");
            submenu.classList.add("opacity-100");
            submenu.style.maxHeight = `${submenu.scrollHeight}px`;
            chevron?.classList.add("rotate-180");
            return;
        }

        submenu.style.maxHeight = "0px";
        submenu.classList.add("opacity-0", "max-h-0");
        submenu.classList.remove("opacity-100");
        chevron?.classList.remove("rotate-180");
    };

    shell.querySelectorAll("[data-submenu-toggle]").forEach((toggle) => {
        const targetId = toggle.getAttribute("aria-controls");
        const submenu = targetId ? shell.querySelector(`#${targetId}`) : null;
        if (!submenu) {
            return;
        }

        setSubmenuState(
            toggle,
            submenu,
            toggle.getAttribute("aria-expanded") === "true",
        );

        toggle.addEventListener("click", () => {
            const expanded = toggle.getAttribute("aria-expanded") === "true";
            setSubmenuState(toggle, submenu, !expanded);
        });
    });

    const openModal = (modal) => {
        const panel = modal.querySelector("[data-modal-panel]");
        modal.setAttribute("aria-hidden", "false");
        modal.classList.remove("pointer-events-none", "opacity-0");
        requestAnimationFrame(() => {
            panel?.classList.remove("translate-y-4", "opacity-0");
        });
    };

    const closeModal = (modal) => {
        const panel = modal.querySelector("[data-modal-panel]");
        panel?.classList.add("translate-y-4", "opacity-0");
        modal.classList.add("opacity-0");
        window.setTimeout(() => {
            modal.setAttribute("aria-hidden", "true");
            modal.classList.add("pointer-events-none");
        }, 280);
    };

    const modals = Array.from(shell.querySelectorAll("[data-modal]"));
    shell.querySelectorAll("[data-modal-open]").forEach((trigger) => {
        trigger.addEventListener("click", () => {
            const modalId = trigger.getAttribute("data-modal-open");
            const modal = modalId ? shell.querySelector(`#${modalId}`) : null;
            if (modal) {
                openModal(modal);
            }
        });
    });

    modals.forEach((modal) => {
        modal.querySelectorAll("[data-modal-close]").forEach((closer) => {
            closer.addEventListener("click", () => closeModal(modal));
        });
    });

    document.addEventListener("keydown", (event) => {
        if (event.key !== "Escape") {
            return;
        }

        modals.forEach((modal) => {
            if (modal.getAttribute("aria-hidden") === "false") {
                closeModal(modal);
            }
        });
        closeSidebar();
    });

    void loadCustomPageScripts(shell);
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", onReady);
} else {
    onReady();
}
