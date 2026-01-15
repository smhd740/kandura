<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'city_id',
        'name',
        'street',
        'building_number',
        'house_number',
        'details',
        'latitude',
        'longitude',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relationship: Address belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Address belongs to a city
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
 * Address has many Orders
 */
public function orders()
{
    return $this->hasMany(Order::class);
}

    /**
     * Scope: Filter default addresses
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: Filter by city ID
     */
    public function scopeCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope: Search addresses
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('street', 'like', "%{$search}%")
              ->orWhere('details', 'like', "%{$search}%")
              ->orWhereHas('city', function ($cityQuery) use ($search) {
                  $cityQuery->where('name->ar', 'like', "%{$search}%")
                           ->orWhere('name->en', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope: Sort addresses
     */
    public function scopeSort($query, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        $allowedSortFields = ['name', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Accessor: Get full address as a string
     */
    public function getFullAddressAttribute()
    {
        $cityName = $this->city ? $this->city->name : '';

        $parts = array_filter([
            $this->building_number,
            $this->street,
            $cityName,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Accessor: Get coordinates as array
     */
    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }

        return null;
    }

    /**
     * Model Events
     */
        // When setting an address as default, remove default from other addresses
    protected static function booted(): void
{
    Address::creating(function ($address) {
        if ($address->is_default) {
            Address::where('user_id', $address->user_id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    });

    Address::updating(function ($address) {
        if ($address->is_default && $address->isDirty('is_default')) {
            Address::where('user_id', $address->user_id)
                ->where('id', '!=', $address->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    });
}
}
