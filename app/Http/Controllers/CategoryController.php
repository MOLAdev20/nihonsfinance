<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $categories = CategoryModel::query()
            ->orderByDesc('id')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'categories' => $categories,
            ]);
        }

        return view('admin.category', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->validateCategoryData($request);

        $category = CategoryModel::query()->create($validatedData);

        return response()->json([
            'message' => 'Data kategori berhasil disimpan.',
            'category' => $category,
        ]);
    }

    public function update(Request $request, CategoryModel $category): JsonResponse
    {
        $validatedData = $this->validateCategoryData($request);

        $category->update($validatedData);

        return response()->json([
            'message' => 'Data kategori berhasil diperbarui.',
            'category' => $category->fresh(),
        ]);
    }

    public function destroy(CategoryModel $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'message' => 'Data kategori berhasil dihapus.',
        ]);
    }

    private function validateCategoryData(Request $request): array
    {
        return $request->validate(
            [
                'title' => ['required', 'string', 'max:255'],
                'type' => ['required', 'in:income,expense'],
            ],
            [
                'title.required' => 'Bidang ini wajib diisi.',
                'title.string' => 'Nama kategori tidak valid.',
                'title.max' => 'Nama kategori maksimal 255 karakter.',
                'type.required' => 'Bidang ini wajib diisi.',
                'type.in' => 'Jenis kategori tidak valid.',
            ],
        );
    }
}
