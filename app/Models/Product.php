<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'buy_price',
        'sell_price',
        'stock',
        'min_stock',
        'unit',
        'expired_date',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'expired_date' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function isExpired(): bool
    {
        return $this->expired_date && $this->expired_date->isPast() && !$this->expired_date->isToday();
    }

    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expired_date) {
            return null;
        }
        return (int) now()->startOfDay()->diffInDays($this->expired_date->startOfDay(), false);
    }

    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expired_date) {
            return 'no_expiry';
        }

        $days = $this->days_until_expiration;

        if ($days < 0) {
            return 'expired'; // Sudah lewat
        }

        if ($days <= 7) {
            return 'critical_7'; // Segera kedaluwarsa (<= 7 hari)
        }

        if ($days <= 30) {
            return 'warning_30'; // Peringatan (<= 30 hari)
        }

        return 'safe';
    }
}

