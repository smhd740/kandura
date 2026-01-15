<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'size',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relationship: Measurement belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Measurement can be used in many designs
     */
    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    /**
 * Measurement belongs to many Order Items
 */
public function orderItems()
{
    return $this->belongsToMany(OrderItem::class, 'measurement_order_item')
        ->withTimestamps();
}

    /**
     * Scope: Filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by size
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Scope: Search measurements
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('size', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
    }

    /**
     * Get available sizes
     */
    public static function availableSizes(): array
    {
        return ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    }
}
