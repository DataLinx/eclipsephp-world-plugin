<?php

namespace Eclipse\World\Models;

use Eclipse\World\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'world_regions';

    protected $fillable = [
        'code',
        'parent_id',
        'is_special',
        'name',
    ];

    protected $casts = [
        'is_special' => 'boolean',
    ];

    /**
     * Get the parent region.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the child regions.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the countries in the region.
     */
    public function countries(): HasMany
    {
        return $this->hasMany(Country::class, 'region_id');
    }

    /**
     * Get the countries in the special region.
     */
    public function specialCountries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'world_country_in_special_region', 'region_id', 'country_id')
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }

    /**
     * Get the countries in the special region at a given date.
     */
    public function getCountriesInSpecialRegion(?Carbon $date = null): Collection
    {
        if (! $this->is_special) {
            return new Collection;
        }

        $checkDate = $date ?? Carbon::now();

        return $this->specialCountries()
            ->wherePivot('start_date', '<=', $checkDate->toDateString())
            ->where(fn ($query) => $query
                ->whereNull('world_country_in_special_region.end_date')
                ->orWhere('world_country_in_special_region.end_date', '>=', $checkDate->toDateString())
            )
            ->get();
    }

    /**
     * Check if the region is geographical.
     */
    public function isGeographical(): bool
    {
        return ! $this->is_special;
    }

    /**
     * Get all the descendants of the region.
     */
    public function getAllDescendants(): Collection
    {
        $descendants = new Collection;

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): RegionFactory
    {
        return RegionFactory::new();
    }
}
