<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Design extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'is_active',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['name', 'description'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relationship: Design belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Design has many measurements (many-to-many)
     */
    public function measurements()
    {
        return $this->belongsToMany(Measurement::class, 'design_measurements')
                    ->withTimestamps();
    }

    /**
     * Relationship: Design has many images
     */
    public function images()
    {
        return $this->hasMany(DesignImage::class)->orderBy('order');
    }

    /**
     * Relationship: Get primary image
     */
    public function primaryImage()
    {
        return $this->hasOne(DesignImage::class)->where('is_primary', true);
    }

    /**
     * Relationship: Design has many option selections
     */
    public function optionSelections()
    {
        return $this->hasMany(DesignOptionSelection::class);
    }

    /**
     * Relationship: Design has many design options (through selections)
     */
    public function designOptions()
    {
        return $this->belongsToMany(DesignOption::class, 'design_option_selections')
                    ->withPivot('custom_value')
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
     * Scope: Filter only active designs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by measurement/size
     */
    public function scopeBySize($query, $size)
    {
        return $query->whereHas('measurements', function ($q) use ($size) {
            $q->where('size', $size);
        });
    }

    /**
     * Scope: Filter by price range
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Scope: Filter by design option
     */
    public function scopeByDesignOption($query, $designOptionId)
    {
        return $query->whereHas('designOptions', function ($q) use ($designOptionId) {
            $q->where('design_options.id', $designOptionId);
        });
    }

    /**
     * Scope: Search designs
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name->ar', 'like', "%{$search}%")
              ->orWhere('name->en', 'like', "%{$search}%")
              ->orWhere('description->ar', 'like', "%{$search}%")
              ->orWhere('description->en', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope: Sort designs
     */
    public function scopeSort($query, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        $allowedSortFields = ['price', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Accessor: Get primary image URL
     */
    public function getPrimaryImageUrlAttribute()
    {
        $primaryImage = $this->primaryImage;
        if ($primaryImage) {
            return $primaryImage->image_url;
        }

        // If no primary image, get first image
        $firstImage = $this->images()->first();
        return $firstImage ? $firstImage->image_url : null;
    }
}
