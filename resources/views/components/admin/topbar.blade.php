<header class="sticky top-0 z-50 border-b border-rose-100/80 bg-white/95 shadow-sm backdrop-blur-sm">
    <div class="mx-auto flex h-16 max-w-[1600px] items-center justify-between px-4 lg:px-6">
        <div class="flex items-center gap-3">
            <button
                aria-controls="admin-sidebar"
                aria-expanded="false"
                aria-label="Toggle Sidebar"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-rose-100 bg-white text-slate-600 shadow-sm transition hover:bg-rose-50 lg:hidden"
                data-sidebar-toggle
                type="button"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M4 7h16M4 12h16M4 17h10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <a class="inline-flex items-center gap-2" href="{{ route('admin.dashboard') }}">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100 text-sm font-bold text-rose-700">
                    NF
                </span>
                <span class="text-sm font-semibold tracking-wide text-slate-800 sm:text-base">
                    Nihons Finance
                </span>
            </a>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-sm font-medium text-slate-700">Admin Nihons</p>
                <p class="text-xs text-slate-500">Super Admin</p>
            </div>
            <img
                alt="Profile Picture"
                class="h-10 w-10 rounded-full border border-rose-200/70 bg-rose-100 object-cover shadow-sm"
                src="{{ asset('images/profile-admin.svg') }}"
            >
        </div>
    </div>
</header>
