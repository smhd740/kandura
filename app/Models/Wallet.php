<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Add balance to wallet
     */
    public function addBalance(float $amount): void
    {
        $this->increment('amount', $amount);
    }

    /**
     * Deduct balance from wallet
     */
    public function deductBalance(float $amount): bool
    {
        if ($this->amount >= $amount) {
            $this->decrement('amount', $amount);
            return true;
        }
        return false;
    }

    /**
     * Check if wallet has sufficient balance
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->amount >= $amount;
    }
}
