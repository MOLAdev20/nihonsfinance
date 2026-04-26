<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
