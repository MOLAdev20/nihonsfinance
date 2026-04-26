<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Admin Dashboard')</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-rose-50 text-slate-700 antialiased">
        <div class="admin-canvas min-h-screen" data-admin-shell>
            <x-admin.topbar />

            <div class="relative flex min-h-[calc(100vh-4rem)] gap-5 px-4 pb-4 pt-4 lg:px-0 lg:pb-0 lg:pt-0">
                <x-admin.sidebar />

                <main class="min-w-0 flex-1">
                    <section class="flex min-h-full flex-col border border-rose-100/60 bg-white/95 p-5 shadow-sm backdrop-blur-sm sm:p-6">
                        <header class="border-b border-rose-100/70 pb-4">
                            <h1 class="text-xl font-semibold tracking-tight text-slate-800 sm:text-2xl">
                                @yield('page_header', 'Dashboard')
                            </h1>
                            <p class="mt-1 text-sm text-slate-500">
                                @yield('page_subheader', 'Halaman dasar dashboard admin.')
                            </p>
                        </header>

                        <div class="flex-1 pt-5">
                            @yield('content')
                        </div>
                    </section>
                </main>
            </div>

            <div
                aria-hidden="true"
                class="pointer-events-none fixed inset-0 z-30 bg-slate-900/35 opacity-0 transition-opacity duration-300 lg:hidden"
                data-sidebar-overlay
            ></div>

            @yield('modals')
        </div>
    </body>
</html>
