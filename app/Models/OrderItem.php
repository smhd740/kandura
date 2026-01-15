<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'design_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships

    /**
     * OrderItem belongs to an Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * OrderItem belongs to a Design
     */
    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    /**
     * OrderItem has many Measurements (Many-to-Many)
     */
    public function measurements()
    {
        return $this->belongsToMany(Measurement::class, 'measurement_order_item')
            ->withTimestamps();
    }

    /**
     * OrderItem has many Design Options (Many-to-Many)
     */
    public function designOptions()
    {
        return $this->belongsToMany(DesignOption::class, 'design_option_order_item')
            ->withTimestamps();
    }

    // Helper Methods

    /**
     * Calculate subtotal
     */
    public function calculateSubtotal(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Set subtotal automatically
     */
    protected static function booted()
    {
        static::saving(function ($orderItem) {
            $orderItem->subtotal = $orderItem->calculateSubtotal();
        });
    }
}
