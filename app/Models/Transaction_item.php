<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction_item extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'user_id',
        'size',
        'color',
        'name',
        'product_id',
        'price',
        'quantity',
        'total',
    ];
}

