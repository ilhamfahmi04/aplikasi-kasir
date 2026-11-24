<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'total_items',
        'total_amount',
        'total_cost',
        'total_profit',
        'cash_amount',
        'change_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_items' => 'integer',
            'total_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'total_profit' => 'decimal:2',
            'cash_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}

