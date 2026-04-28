<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceModel extends Model
{
    protected $table = 'invoice';

    protected $fillable = [
        'customer_id',
        'invoice_code',
        'issue_date',
        'due_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLineModel::class, 'invoice_id');
    }
}
