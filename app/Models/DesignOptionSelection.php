<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignOptionSelection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'design_id',
        'design_option_id',
        'custom_value',
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
     * Relationship: Selection belongs to a design
     */
    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    /**
     * Relationship: Selection belongs to a design option
     */
    public function designOption()
    {
        return $this->belongsTo(DesignOption::class);
    }
}
