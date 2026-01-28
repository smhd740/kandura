<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Review belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Review belongs to Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
