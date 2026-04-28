<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\CustomerModel;
use App\Models\TransactionModel;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    private const STATUS_DRAFT = 'draft';

    private const STATUS_UNPAID = 'unpaid';

    private const STATUS_PAID = 'paid';

    private const STATUS_CANCELED = 'canceled';

    public function index(): View
    {
        $invoices = InvoiceModel::query()
            ->with(['customer:id,full_name'])
            ->withCount('lines')
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.invoice.invoice', [
            'invoices' => $invoices,
        ]);
    }

    public function create(): View
    {
        return view('admin.invoice.invoice-form', $this->getInvoiceFormData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validateInvoiceData($request);
        $itemPayload = $this->buildItemPayload($validatedData['items']);

        DB::transaction(function () use ($validatedData, $itemPayload): void {
            $invoice = InvoiceModel::query()->create([
                'customer_id' => $validatedData['customer_id'],
                'invoice_code' => $validatedData['invoice_code'],
                'issue_date' => $validatedData['issue_date'],
                'due_date' => $validatedData['due_date'],
                'status' => 'draft',
                'total_amount' => $itemPayload['totalAmount'],
            ]);

            $invoice->lines()->createMany($itemPayload['lines']);
        });

        return redirect()
            ->route('admin.invoice.index')
            ->with('successMessage', 'Invoice berhasil disimpan.');
    }

    public function show(InvoiceModel $invoice): View
    {
        $invoice->load([
            'customer:id,full_name,email,address',
            'lines.product:id,title,price',
        ]);

        $statusOptions = $this->getStatusOptions();

        return view('admin.invoice.invoice-detail', [
            'invoice' => $invoice,
            'statusOptions' => $statusOptions,
            'currentStatusMeta' => $this->getStatusMeta($invoice->status, $statusOptions),
        ]);
    }

    public function edit(InvoiceModel $invoice): View
    {
        $invoice->load(['customer:id,full_name,email,address', 'lines.product:id,title,price']);

        return view('admin.invoice.invoice-form-update', [
            ...$this->getInvoiceFormData(),
            'invoice' => $invoice,
        ]);
    }

    public function update(Request $request, InvoiceModel $invoice): RedirectResponse
    {
        $validatedData = $this->validateInvoiceData($request, $invoice->id);
        $itemPayload = $this->buildItemPayload($validatedData['items']);

        DB::transaction(function () use ($invoice, $validatedData, $itemPayload): void {
            $invoice->update([
                'customer_id' => $validatedData['customer_id'],
                'invoice_code' => $validatedData['invoice_code'],
                'issue_date' => $validatedData['issue_date'],
                'due_date' => $validatedData['due_date'],
                'status' => $invoice->status,
                'total_amount' => $itemPayload['totalAmount'],
            ]);

            $invoice->lines()->delete();
            $invoice->lines()->createMany($itemPayload['lines']);
        });

        return redirect()
            ->route('admin.invoice.show', $invoice)
            ->with('successMessage', 'Invoice berhasil diperbarui.');
    }

    public function destroy(InvoiceModel $invoice): RedirectResponse
    {
        DB::transaction(function () use ($invoice): void {
            $invoice->lines()->delete();
            $invoice->delete();
        });

        return redirect()
            ->route('admin.invoice.index')
            ->with('successMessage', 'Invoice berhasil dihapus.');
    }

    public function generatePdf(Request $request)
    {
        $validatedData = $request->validate(
            [
                'invoice' => ['required', 'integer', 'exists:invoice,id'],
            ],
            [
                'invoice.required' => 'Invoice wajib dipilih.',
                'invoice.integer' => 'Invoice tidak valid.',
                'invoice.exists' => 'Invoice tidak ditemukan.',
            ]
        );

        $invoice = InvoiceModel::query()
            ->with([
                'customer:id,full_name,email,address',
                'lines.product:id,title,price',
            ])
            ->findOrFail((int) $validatedData['invoice']);

        if ($invoice->lines->count() === 0) {
            return redirect()
                ->route('admin.invoice.show', $invoice)
                ->with('errorMessage', 'Invoice belum memiliki item sehingga PDF tidak dapat dibuat.');
        }

        $pdf = Pdf::loadView('admin.invoice.invoice-pdf', [
            'invoice' => $invoice,
            'statusMeta' => $this->getStatusMeta($invoice->status),
        ])
            ->setOption([
                'isRemoteEnabled' => false,
                'isFontSubsettingEnabled' => false,
                'defaultFont' => 'sans-serif',
            ])
            ->setPaper('a4', 'portrait');

        $safeInvoiceCode = preg_replace('/[^A-Za-z0-9\-_]/', '-', (string) $invoice->invoice_code) ?: 'invoice';

        return $pdf->stream('invoice-' . $safeInvoiceCode . '.pdf');
        // return view("admin.invoice.invoice-pdf", [
        //     'invoice' => $invoice,
        //     'statusMeta' => $this->getStatusMeta($invoice->status),
        // ]);
    }

    public function updateStatus(Request $request, InvoiceModel $invoice): RedirectResponse
    {
        $validatedData = $request->validate(
            [
                'status' => ['required', 'string', Rule::in($this->getUpdatableStatuses())],
            ],
            [
                'status.required' => 'Status invoice wajib dipilih.',
                'status.string' => 'Status invoice tidak valid.',
                'status.in' => 'Status invoice tidak tersedia.',
            ]
        );

        $targetStatus = $validatedData['status'];
        $previousStatus = (string) $invoice->status;

        if ($previousStatus === $targetStatus) {
            return redirect()
                ->route('admin.invoice.show', $invoice)
                ->with('successMessage', 'Status invoice tidak berubah.');
        }

        DB::transaction(function () use ($invoice, $previousStatus, $targetStatus): void {
            $invoice->update([
                'status' => $targetStatus,
            ]);

            $invoice->loadMissing('customer:id,full_name');
            $this->handlePaidStatusTransition($invoice, $previousStatus, $targetStatus);
        });

        return redirect()
            ->route('admin.invoice.show', $invoice)
            ->with('successMessage', 'Status invoice berhasil diperbarui.');
    }

    private function getInvoiceFormData(): array
    {
        $customers = CustomerModel::query()
            ->orderBy('full_name', 'desc')
            ->get(['id', 'full_name', 'email', 'address']);

        $products = ProductModel::query()
            ->orderBy('title', 'desc')
            ->get(['id', 'title', 'price']);

        return [
            'customers' => $customers,
            'products' => $products,
        ];
    }

    private function validateInvoiceData(Request $request, ?int $invoiceId = null): array
    {
        $invoiceCodeUniqueRule = 'unique:invoice,invoice_code';
        if ($invoiceId) {
            $invoiceCodeUniqueRule .= ',' . $invoiceId;
        }

        return $request->validate(
            [
                'customer_id' => ['required', 'integer', 'exists:customer,id'],
                'invoice_code' => ['required', 'string', 'max:255', $invoiceCodeUniqueRule],
                'issue_date' => ['required', 'date'],
                'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', 'integer', 'exists:product,id'],
                'items.*.qty' => ['required', 'integer', 'min:1'],
            ],
            [
                'customer_id.required' => 'Customer wajib dipilih.',
                'customer_id.integer' => 'Customer tidak valid.',
                'customer_id.exists' => 'Customer tidak ditemukan.',
                'invoice_code.required' => 'Kode invoice wajib diisi.',
                'invoice_code.string' => 'Kode invoice tidak valid.',
                'invoice_code.max' => 'Kode invoice maksimal 255 karakter.',
                'invoice_code.unique' => 'Kode invoice sudah digunakan.',
                'issue_date.required' => 'Issue date wajib diisi.',
                'issue_date.date' => 'Issue date tidak valid.',
                'due_date.required' => 'Due date wajib diisi.',
                'due_date.date' => 'Due date tidak valid.',
                'due_date.after_or_equal' => 'Due date tidak boleh lebih awal dari issue date.',
                'items.required' => 'Item invoice wajib diisi.',
                'items.array' => 'Format item invoice tidak valid.',
                'items.min' => 'Invoice minimal memiliki satu item.',
                'items.*.product_id.required' => 'Produk wajib dipilih.',
                'items.*.product_id.integer' => 'Produk tidak valid.',
                'items.*.product_id.exists' => 'Produk tidak ditemukan.',
                'items.*.qty.required' => 'Qty wajib diisi.',
                'items.*.qty.integer' => 'Qty harus berupa angka bulat.',
                'items.*.qty.min' => 'Qty minimal 1.',
            ]
        );
    }

    private function buildItemPayload(array $items): array
    {
        $productIds = collect($items)
            ->pluck('product_id')
            ->map(fn($value): int => (int) $value)
            ->unique()
            ->values();

        /** @var Collection<int, ProductModel> $products */
        $products = ProductModel::query()
            ->whereIn('id', $productIds)
            ->get(['id', 'price'])
            ->keyBy('id');

        if ($products->count() !== $productIds->count()) {
            throw ValidationException::withMessages([
                'items' => 'Produk invoice tidak valid.',
            ]);
        }

        $lines = collect($items)
            ->values()
            ->map(function (array $item) use ($products): array {
                $productId = (int) $item['product_id'];
                $qty = (int) $item['qty'];
                $unitPrice = (float) ($products->get($productId)?->price ?? 0);
                $subtotal = $qty * $unitPrice;

                return [
                    'product_id' => $productId,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            })
            ->values();

        return [
            'lines' => $lines->all(),
            'totalAmount' => (float) $lines->sum('subtotal'),
        ];
    }

    private function getUpdatableStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_UNPAID,
            self::STATUS_PAID,
            self::STATUS_CANCELED,
        ];
    }

    private function getStatusOptions(): array
    {
        return [
            [
                'value' => self::STATUS_DRAFT,
                'label' => 'Draft',
                'badgeClass' => 'border-slate-200 bg-slate-100 text-slate-700',
                'dotClass' => 'bg-slate-500',
                'selectClass' => 'border-slate-200 bg-slate-50 text-slate-700',
            ],
            [
                'value' => self::STATUS_UNPAID,
                'label' => 'Belum Dibayar',
                'badgeClass' => 'border-blue-200 bg-blue-50 text-blue-700',
                'dotClass' => 'bg-blue-500',
                'selectClass' => 'border-blue-200 bg-blue-50 text-blue-700',
            ],
            [
                'value' => self::STATUS_PAID,
                'label' => 'Dibayar',
                'badgeClass' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                'dotClass' => 'bg-emerald-500',
                'selectClass' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            ],
            [
                'value' => self::STATUS_CANCELED,
                'label' => 'Dibatalkan',
                'badgeClass' => 'border-rose-200 bg-rose-50 text-rose-700',
                'dotClass' => 'bg-rose-500',
                'selectClass' => 'border-rose-200 bg-rose-50 text-rose-700',
            ],
        ];
    }

    private function getStatusMeta(string $status, ?array $statusOptions = null): array
    {
        $options = $statusOptions ?? $this->getStatusOptions();
        if ($status === 'partial') {
            $status = self::STATUS_UNPAID;
        }

        foreach ($options as $option) {
            if ((string) $option['value'] === $status) {
                return $option;
            }
        }

        return [
            'value' => $status,
            'label' => ucfirst($status),
            'badgeClass' => 'border-slate-200 bg-slate-100 text-slate-700',
            'dotClass' => 'bg-slate-500',
            'selectClass' => 'border-slate-200 bg-slate-50 text-slate-700',
        ];
    }

    private function handlePaidStatusTransition(InvoiceModel $invoice, string $previousStatus, string $targetStatus): void
    {
        if ($targetStatus !== self::STATUS_PAID || $previousStatus === self::STATUS_PAID) {
            return;
        }

        $customerName = trim((string) ($invoice->customer?->full_name ?? ''));
        if ($customerName === '') {
            throw ValidationException::withMessages([
                'status' => 'Customer invoice tidak valid.',
            ]);
        }

        $incomeCategory = CategoryModel::query()
            ->where('type', 'income')
            ->orderBy('id')
            ->first();

        if (!$incomeCategory) {
            throw ValidationException::withMessages([
                'status' => 'Kategori pemasukan tidak ditemukan. Tambahkan kategori income terlebih dahulu.',
            ]);
        }

        $incomeAmount = (float) $invoice->total_amount;

        $existingTransaction = TransactionModel::query()
            ->where('category_id', $incomeCategory->id)
            ->where('type', 'income')
            ->where('description', $customerName)
            ->where('amount', $incomeAmount)
            ->exists();

        if ($existingTransaction) {
            return;
        }

        TransactionModel::query()->create([
            'category_id' => $incomeCategory->id,
            'description' => $customerName,
            'amount' => $incomeAmount,
            'type' => 'income',
            'date' => now(),
        ]);
    }
}
