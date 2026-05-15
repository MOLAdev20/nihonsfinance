<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $customers = CustomerModel::query()
            ->orderByDesc('id')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'customers' => $customers->map(fn (CustomerModel $customer) => $this->formatCustomer($customer)),
            ]);
        }

        return view('admin.customer', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->validateCustomerData($request);

        $customer = CustomerModel::query()->create($validatedData);

        return response()->json([
            'message' => 'Data customer berhasil disimpan.',
            'customer' => $this->formatCustomer($customer),
        ]);
    }

    public function update(Request $request, CustomerModel $customer): JsonResponse
    {
        $validatedData = $this->validateCustomerData($request, $customer);

        $customer->update($validatedData);

        return response()->json([
            'message' => 'Data customer berhasil diperbarui.',
            'customer' => $this->formatCustomer($customer->fresh()),
        ]);
    }

    public function destroy(CustomerModel $customer): JsonResponse
    {
        $customer->delete();

        return response()->json([
            'message' => 'Data customer berhasil dihapus.',
        ]);
    }

    private function validateCustomerData(Request $request, ?CustomerModel $customer = null): array
    {
        $request->merge([
            'full_name' => trim((string) $request->input('fullname', $request->input('full_name', ''))),
            'email' => trim((string) $request->input('email', '')),
            'address' => trim((string) $request->input('address', '')),
        ]);

        return $request->validate(
            [
                'full_name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('customer', 'email')->ignore($customer?->id),
                ],
                'address' => ['nullable', 'string'],
            ],
            [
                'full_name.required' => 'Nama lengkap wajib diisi.',
                'full_name.string' => 'Nama lengkap tidak valid.',
                'full_name.max' => 'Nama lengkap maksimal 255 karakter.',
                'email.required' => 'Alamat email wajib diisi.',
                'email.email' => 'Format alamat email tidak valid.',
                'email.max' => 'Alamat email maksimal 255 karakter.',
                'email.unique' => 'Alamat email sudah terdaftar.',
                'address.string' => 'Alamat tidak valid.',
            ],
        );
    }

    private function formatCustomer(CustomerModel $customer): array
    {
        return [
            'id' => $customer->id,
            'full_name' => $customer->full_name,
            'fullname' => $customer->full_name,
            'email' => $customer->email,
            'address' => $customer->address,
        ];
    }
}
