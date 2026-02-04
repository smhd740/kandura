<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'profile_image',
        'is_active',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
 * The "booted" method of the model.
 */
protected static function booted(): void
{
    static::created(function ($user) {
        // إنشاء محفظة تلقائياً لكل user جديد بقيمة 0
        if ($user->role === 'user') {
            $user->wallet()->create(['amount' => 0]);
        }
    });
}
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    /**
 * User has many Orders
 */
public function orders()
{
    return $this->hasMany(Order::class);
}
     // Scope a query to only include active users.
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    //  Scope a query to filter by role.

    // public function scopeRole($query, $role)
    // {
    //     return $query->where('role', $role);
    // }

    public function scopeRole($query, $role)
{
    return $query->whereHas('roles', function ($q) use ($role) {
        $q->where('name', $role);
    });
}


    //Scope a query to search users.

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        });
    }


     // Get the user's profile image URL.

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }

        return asset('images/default-avatar.png');
    }
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
    public function measurements()
{
    return $this->hasMany(Measurement::class);
}

/**
 * Relationship: User has many designs
 */
public function designs()
{
    return $this->hasMany(Design::class);
}

public function wallet()
{
    return $this->hasOne(Wallet::class);
}

public function getOrCreateWallet(): Wallet
{
    return $this->wallet()->firstOrCreate(
        ['user_id' => $this->id],
        ['amount' => 0]
    );
}
public function coupons()
{
    return $this->belongsToMany(Coupon::class, 'coupon_user')
        ->withTimestamps();
}

public function couponUsages()
{
    return $this->hasMany(CouponUsage::class);
}

    public function deviceTokens()
{
    return $this->hasMany(DeviceToken::class);
}

// public function getRoleAttribute()
// {
//     return $this->roles->first()?->name;
// }

}
