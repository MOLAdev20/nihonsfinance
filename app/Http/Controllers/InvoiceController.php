<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $customers = collect();
        $products = collect();

        if (Schema::hasTable('customer')) {
            $customers = CustomerModel::query()
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'email', 'address']);
        }

        if (Schema::hasTable('product')) {
            $products = ProductModel::query()
                ->orderBy('title')
                ->get(['id', 'title', 'price']);
        }

        return view('admin.invoice.invoice-form', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }
}
