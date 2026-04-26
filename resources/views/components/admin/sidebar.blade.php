<aside
    class="fixed inset-y-0 left-0 z-40 mt-16 w-72 -translate-x-full overflow-y-auto border-r border-rose-100/80 bg-white px-4 pb-6 pt-4 shadow-sm transition-transform duration-300 ease-out lg:static lg:mt-0 lg:h-[calc(100vh-4rem)] lg:translate-x-0 lg:border lg:pt-5"
    data-sidebar
    id="admin-sidebar"
>
    <div class="mb-4 flex items-center justify-between lg:hidden">
        <h2 class="text-sm font-semibold tracking-wide text-slate-800">Menu Navigasi</h2>
        <button
            aria-label="Close Sidebar"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-100 text-slate-600 transition hover:bg-rose-50"
            data-sidebar-close
            type="button"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M6 6l12 12M18 6l-12 12" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>

    <nav class="space-y-4">
        <section>
            <p class="px-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Overview</p>
            <ul class="mt-2 space-y-1.5">
                <li>
                    <a class="admin-menu-link admin-menu-active" href="#">
                        <x-admin.icon.grid />
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a class="admin-menu-link" href="#">
                        <x-admin.icon.chart />
                        <span>Ringkasan</span>
                    </a>
                </li>
            </ul>
        </section>

        <section>
            <p class="px-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Keuangan</p>
            <ul class="mt-2 space-y-1.5">
                <li>
                    <button
                        aria-controls="submenu-keuangan"
                        aria-expanded="false"
                        class="admin-menu-link w-full justify-between"
                        data-submenu-toggle
                        type="button"
                    >
                        <span class="inline-flex items-center gap-2.5">
                            <x-admin.icon.wallet />
                            <span>Transaksi</span>
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-300" data-submenu-chevron fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul class="admin-submenu mt-1.5 ml-2 max-h-0 overflow-hidden border-l border-rose-100 pl-4 opacity-0 transition-all duration-300" id="submenu-keuangan">
                        <li>
                            <a class="admin-submenu-link" href="#">Pemasukan</a>
                        </li>
                        <li>
                            <a class="admin-submenu-link" href="#">Pengeluaran</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="admin-menu-link" href="#">
                        <x-admin.icon.card />
                        <span>Budget</span>
                    </a>
                </li>
            </ul>
        </section>

        <section>
            <p class="px-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Master Data</p>
            <ul class="mt-2 space-y-1.5">
                <li>
                    <button
                        aria-controls="submenu-master"
                        aria-expanded="false"
                        class="admin-menu-link w-full justify-between"
                        data-submenu-toggle
                        type="button"
                    >
                        <span class="inline-flex items-center gap-2.5">
                            <x-admin.icon.users />
                            <span>Pengguna</span>
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-300" data-submenu-chevron fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <ul class="admin-submenu mt-1.5 ml-2 max-h-0 overflow-hidden border-l border-rose-100 pl-4 opacity-0 transition-all duration-300" id="submenu-master">
                        <li>
                            <a class="admin-submenu-link" href="#">Admin</a>
                        </li>
                        <li>
                            <a class="admin-submenu-link" href="#">Staff</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="admin-menu-link" href="#">
                        <x-admin.icon.folder />
                        <span>Kategori</span>
                    </a>
                </li>
            </ul>
        </section>

        <section>
            <p class="px-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Pengaturan</p>
            <ul class="mt-2 space-y-1.5">
                <li>
                    <a class="admin-menu-link" href="#">
                        <x-admin.icon.settings />
                        <span>Preferensi</span>
                    </a>
                </li>
                <li>
                    <a class="admin-menu-link" href="#">
                        <x-admin.icon.document />
                        <span>Log Aktivitas</span>
                    </a>
                </li>
            </ul>
        </section>
    </nav>
</aside>
