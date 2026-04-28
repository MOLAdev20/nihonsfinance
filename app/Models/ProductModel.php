<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductModel extends Model
{
    protected $table = 'product';

    protected $fillable = [
        'title',
        'price',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function invoiceLines(): HasMany
    {
        return $this->hasMany(InvoiceLineModel::class, 'product_id');
    }
}
