<x-ui.modal id="add-customer-modal" maxWidth="max-w-lg" title="Tambah Customer">
    <form class="space-y-4" data-invoice-add-customer-form novalidate>
        <label class="flex flex-col gap-1 text-sm text-slate-600">
            <span class="font-medium">Nama Lengkap</span>
            <input
                class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                data-add-customer-input="full_name"
                placeholder="Masukkan nama customer"
                type="text"
            >
            <span class="min-h-4 text-xs text-rose-600" data-add-customer-error="full_name"></span>
        </label>

        <label class="flex flex-col gap-1 text-sm text-slate-600">
            <span class="font-medium">Email</span>
            <input
                class="rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                data-add-customer-input="email"
                placeholder="customer@email.com"
                type="email"
            >
            <span class="min-h-4 text-xs text-rose-600" data-add-customer-error="email"></span>
        </label>

        <label class="flex flex-col gap-1 text-sm text-slate-600">
            <span class="font-medium">Alamat</span>
            <textarea
                class="min-h-24 rounded-xl border border-rose-100 bg-white px-3 py-2 text-slate-700 outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"
                data-add-customer-input="address"
                placeholder="Alamat customer"
            ></textarea>
            <span class="min-h-4 text-xs text-rose-600" data-add-customer-error="address"></span>
        </label>

        <div class="flex items-center justify-end gap-2 border-t border-rose-100 pt-3">
            <button
                class="rounded-xl border border-rose-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-rose-50"
                data-modal-close
                type="button"
            >
                Batal
            </button>
            <button
                class="rounded-xl bg-rose-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-rose-600"
                type="submit"
            >
                Simpan Customer
            </button>
        </div>
    </form>
</x-ui.modal>
