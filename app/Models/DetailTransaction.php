<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\DetailTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'invoice_number',
        'product_code',
        'product_name',
        'price_at_time',
        'disc1',
        'disc2',
        'disc3',
        'net_price',
        'quantity',
        'amount',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
