<x-guest-layout>
    <div class="mb-5 border-b border-rose-100 pb-4">
        <h1 class="text-xl font-semibold tracking-tight text-slate-800">Login Admin</h1>
        <p class="mt-1 text-sm text-slate-500">Masuk untuk mengakses dashboard internal perusahaan.</p>
    </div>

    @if ($errors->has('username'))
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
            {{ $errors->first('username') }}
        </div>
    @endif

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form class="space-y-4" method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="username" :value="'Username'" />
            <x-text-input
                id="username"
                class="px-3 py-2 mt-1 block w-full rounded-lg border-rose-100 focus:border-rose-300 focus:ring-rose-100"
                type="text"
                name="username"
                :value="old('username')"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan username"
            />
        </div>

        <div>
            <x-input-label for="password" :value="'Password'" />
            <div class="relative mt-1">
                <x-text-input
                    id="password"
                    class="px-3 py-2 block w-full rounded-lg border-rose-100 pr-11 focus:border-rose-300 focus:ring-rose-100"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                />

                <button
                    class="absolute inset-y-0 right-0 inline-flex w-11 items-center justify-center text-slate-500 transition hover:text-slate-700"
                    data-password-toggle
                    type="button"
                    aria-label="Tampilkan atau sembunyikan password"
                >
                    <svg data-eye-open class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M2.5 12s3.5-6.5 9.5-6.5 9.5 6.5 9.5 6.5-3.5 6.5-9.5 6.5S2.5 12 2.5 12Z" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg data-eye-closed class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M3 3l18 18" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9.9 5.2A9.5 9.5 0 0 1 12 5c6 0 9.5 7 9.5 7a16.7 16.7 0 0 1-4 4.8" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6.1 6.1A16.2 16.2 0 0 0 2.5 12S6 19 12 19c1.8 0 3.4-.6 4.9-1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center rounded-xl bg-rose-500 py-2.5 text-sm font-medium tracking-wide text-white transition hover:bg-rose-600">
            Login
        </x-primary-button>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const passwordInput = document.querySelector("#password");
            const toggleButton = document.querySelector("[data-password-toggle]");
            const eyeOpen = document.querySelector("[data-eye-open]");
            const eyeClosed = document.querySelector("[data-eye-closed]");

            if (!passwordInput || !toggleButton || !eyeOpen || !eyeClosed) {
                return;
            }

            toggleButton.addEventListener("click", () => {
                const isHidden = passwordInput.getAttribute("type") === "password";
                passwordInput.setAttribute("type", isHidden ? "text" : "password");
                eyeOpen.classList.toggle("hidden", isHidden);
                eyeClosed.classList.toggle("hidden", !isHidden);
            });
        });
    </script>
</x-guest-layout>
