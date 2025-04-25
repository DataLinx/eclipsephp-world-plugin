<?php

namespace Eclipse\World\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Eclipse\World\Factories\RegionFactory;

class Region extends Model
{
    use HasFactory;

    protected $table = 'world_regions';

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'is_special',
    ];

    protected $casts = [
        'is_special' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subRegions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class, 'region_id');
    }

    public function specialCountries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'world_country_in_special_region', 'region_id', 'country_id')
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }

    protected static function newFactory(): RegionFactory
    {
        return RegionFactory::new();
    }
}
