@props([
    'id',
    'title' => '',
    'maxWidth' => 'max-w-xl',
])

<div
    aria-hidden="true"
    class="pointer-events-none fixed inset-0 z-[120] opacity-0 transition-opacity duration-300"
    data-modal
    id="{{ $id }}"
    role="dialog"
>
    <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
        <div class="relative w-full {{ $maxWidth }} translate-y-4 rounded-2xl border border-rose-100 bg-white shadow-lg opacity-0 transition-all duration-300" data-modal-panel>
            <div class="flex items-start justify-between border-b border-rose-100 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
                <button
                    aria-label="Close modal"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-slate-700"
                    data-modal-close
                    type="button"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M6 6l12 12M18 6l-12 12" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="px-5 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
