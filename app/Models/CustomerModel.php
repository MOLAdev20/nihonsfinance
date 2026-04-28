<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerModel extends Model
{
    protected $table = 'customer';

    protected $fillable = [
        'full_name',
        'email',
        'address',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(InvoiceModel::class, 'customer_id');
    }
}
