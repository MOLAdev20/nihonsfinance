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
                    <a class="admin-menu-link {{ request()->routeIs('admin.dashboard') ? 'admin-menu-active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <x-admin.icon.grid />
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </section>

        <section>
            <p class="px-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Keuangan</p>
            <ul class="mt-2 space-y-1.5">
                <li>
                    <a class="admin-menu-link {{ request()->routeIs('admin.transaction.index') ? 'admin-menu-active' : '' }}" href="{{ route('admin.transaction.index') }}">
                        <x-admin.icon.card />
                        <span>Transaksi</span>
                    </a>
                </li>
                <li>
                    <a class="admin-menu-link {{ request()->routeIs('admin.invoice.*') ? 'admin-menu-active' : '' }}" href="{{ route('admin.invoice.index') }}">
                        <x-admin.icon.document />
                        <span>Invoice</span>
                    </a>
                </li>
            </ul>
        </section>

        <section>
            <p class="px-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Master Data</p>
            <ul class="mt-2 space-y-1.5">
                <li>
                    <a class="admin-menu-link {{ request()->routeIs('admin.product.*') ? 'admin-menu-active' : '' }}" href="{{ route('admin.product.index') }}">
                        <x-admin.icon.folder />
                        <span>Produk</span>
                    </a>
                </li>
                <li>
                    <a class="admin-menu-link {{ request()->routeIs('admin.category.*') ? 'admin-menu-active' : '' }}" href="{{ route('admin.category.index') }}">
                        <x-admin.icon.folder />
                        <span>Kategori</span>
                    </a>
                </li>
                <li>
                    <a class="admin-menu-link {{ request()->routeIs('admin.customer.*') ? 'admin-menu-active' : '' }}" href="{{ route('admin.customer.index') }}">
                        <x-admin.icon.folder />
                        <span>Master Pelanggan</span>
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
