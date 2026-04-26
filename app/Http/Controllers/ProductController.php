<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = ProductModel::query()
            ->orderByDesc('id')
            ->get();

        return view('admin.product', [
            'products' => $products,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->validateProductData($request);

        $product = ProductModel::query()->create($validatedData);

        return response()->json([
            'message' => 'Data produk berhasil disimpan.',
            'product' => $product,
        ]);
    }

    public function update(Request $request, ProductModel $product): JsonResponse
    {
        $validatedData = $this->validateProductData($request);

        $product->update($validatedData);

        return response()->json([
            'message' => 'Data produk berhasil diperbarui.',
            'product' => $product->fresh(),
        ]);
    }

    public function destroy(ProductModel $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Data produk berhasil dihapus.',
        ]);
    }

    private function validateProductData(Request $request): array
    {
        return $request->validate(
            [
                'title' => ['required', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'description' => ['nullable', 'string', 'min:3', 'max:255'],
            ],
            [
                'title.required' => 'Nama Produk/Jasa wajib diisi.',
                'title.string' => 'Nama Produk/Jasa tidak valid.',
                'price.required' => 'Harga wajib diisi.',
                'price.numeric' => 'Harga harus berupa angka.',
                'price.min' => 'Harga tidak boleh kurang dari nol.',
                'description.string' => 'Deskripsi Produk tidak valid.',
                'description.min' => 'Deskripsi Produk minimal 3 karakter.',
                'description.max' => 'Deskripsi Produk maksimal 255 karakter.',
            ]
        );
    }
}
