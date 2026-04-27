<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\TransactionModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $transactions = TransactionModel::query()
            ->with('category:id,title,type')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $categories = CategoryModel::query()
            ->orderBy('title')
            ->get(['id', 'title', 'type']);

        if ($request->expectsJson()) {
            return response()->json([
                'transactions' => $transactions,
            ]);
        }

        return view('admin.transaction', [
            'transactions' => $transactions,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->validateTransactionData($request);

        $transaction = TransactionModel::query()->create($validatedData);

        return response()->json([
            'message' => 'Data transaksi berhasil disimpan.',
            'transaction' => $transaction->load('category:id,title,type'),
        ]);
    }

    public function update(
        Request $request,
        TransactionModel $transaction
    ): JsonResponse {
        $validatedData = $this->validateTransactionData($request);

        $transaction->update($validatedData);

        return response()->json([
            'message' => 'Data transaksi berhasil diperbarui.',
            'transaction' => $transaction->fresh()->load('category:id,title,type'),
        ]);
    }

    public function destroy(TransactionModel $transaction): JsonResponse
    {
        $transaction->delete();

        return response()->json([
            'message' => 'Data transaksi berhasil dihapus.',
        ]);
    }

    private function validateTransactionData(Request $request): array
    {
        $validatedData = $request->validate(
            [
                'category_id' => ['required', 'integer', 'exists:category,id'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'date' => ['required', 'date'],
                'type' => ['required', 'in:income,expense'],
                'description' => ['required', 'string', 'min:3', 'max:500'],
            ],
            [
                'category_id.required' => 'Bidang ini wajib diisi.',
                'category_id.integer' => 'Transaksi tidak valid.',
                'category_id.exists' => 'Transaksi tidak ditemukan.',
                'amount.required' => 'Bidang ini wajib diisi.',
                'amount.numeric' => 'Jumlah harus berupa angka.',
                'amount.min' => 'Jumlah harus lebih besar dari nol.',
                'date.required' => 'Bidang ini wajib diisi.',
                'date.date' => 'Tanggal transaksi tidak valid.',
                'type.required' => 'Bidang ini wajib diisi.',
                'type.in' => 'Jenis transaksi tidak valid.',
                'description.required' => 'Bidang ini wajib diisi.',
                'description.string' => 'Deskripsi tidak valid.',
                'description.min' => 'Deskripsi minimal 3 karakter.',
                'description.max' => 'Deskripsi maksimal 500 karakter.',
            ],
        );

        $category = CategoryModel::query()->find($validatedData['category_id']);
        if (!$category) {
            throw ValidationException::withMessages([
                'category_id' => 'Transaksi tidak ditemukan.',
            ]);
        }

        if ($validatedData['type'] !== $category->type) {
            throw ValidationException::withMessages([
                'type' => 'Jenis transaksi harus mengikuti kategori.',
            ]);
        }

        $validatedData['type'] = $category->type;

        return $validatedData;
    }
}
