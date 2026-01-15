<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'total_amount',
        'status',
        'notes',
        'payment_method',
        'payment_status',
        'stripe_payment_intent_id',
        'paid_at',
        'coupon_id',
        'discount_amount',
        'subtotal',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships

    /**
     * Order belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order belongs to an Address
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Order has many Items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Search by order number or user name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('order_number', 'like', "%{$search}%")
            ->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Scope: Filter by price range
     */
    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('total_amount', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('total_amount', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Scope: Sort orders
     */
    public function scopeSort($query, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Scope: Active statuses (not cancelled)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'processing', 'completed']);
    }

    // Helper Methods

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if order status can be updated
     */
    public function canUpdateStatus(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastOrder ? (int) substr($lastOrder->order_number, -3) + 1 : 1;

        return 'ORD-' . $date . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function transactions()
{
    return $this->hasMany(Transaction::class);
}

/**
 * Check if order is paid
 */
public function isPaid(): bool
{
    return $this->payment_status === 'paid';
}

/**
 * Mark order as paid
 */
public function markAsPaid(): void
{
    $this->update([
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);
}

public function coupon()
{
    return $this->belongsTo(Coupon::class);
}
}
