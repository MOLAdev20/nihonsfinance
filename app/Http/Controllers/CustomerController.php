<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate(
            [
                'full_name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255', 'unique:customer,email'],
                'address' => ['nullable', 'string'],
            ],
            [
                'full_name.required' => 'Nama Lengkap wajib diisi.',
                'full_name.string' => 'Nama Lengkap tidak valid.',
                'full_name.max' => 'Nama Lengkap maksimal 255 karakter.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 255 karakter.',
                'email.unique' => 'Email customer sudah terdaftar.',
                'address.string' => 'Alamat tidak valid.',
            ],
        );

        $customer = CustomerModel::query()->create($validatedData);

        return response()->json([
            'message' => 'Customer berhasil ditambahkan.',
            'customer' => $customer->only(['id', 'full_name', 'email', 'address']),
        ]);
    }
}
