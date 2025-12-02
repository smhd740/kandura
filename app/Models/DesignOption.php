<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class DesignOption extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'image',
        'is_active',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['name'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relationship: Design option has many selections
     */
    public function selections()
    {
        return $this->hasMany(DesignOptionSelection::class);
    }

    /**
     * Relationship: Design option can be used in many designs (through selections)
     */
    public function designs()
    {
        return $this->belongsToMany(Design::class, 'design_option_selections')
                    ->withPivot('custom_value')
                    ->withTimestamps();
    }

    /**
     * Scope: Filter only active options
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Search design options
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name->ar', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
    }

    /**
     * Scope: Sort design options
     */
    public function scopeSort($query, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        $allowedSortFields = ['type', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Get available types
     */
    public static function availableTypes(): array
    {
        return ['color', 'fabric_type', 'sleeve_type', 'dome_type'];
    }

    /**
     * Accessor: Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}
