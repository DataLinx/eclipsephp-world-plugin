<?php

namespace Eclipse\World\Models;

use Eclipse\World\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'world_countries';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'a3_id',
        'num_code',
        'name',
        'flag',
        'region_id',
    ];

    /**
     * Get the region that the country belongs to.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Get the special regions that the country belongs to.
     */
    public function specialRegions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'world_country_in_special_region', 'country_id', 'region_id')
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }

    /**
     * Check if the country belongs to a special region at a given date.
     */
    public function belongsToSpecialRegion(Region $region, ?Carbon $date = null): bool
    {
        if (! $region->is_special) {
            return false;
        }

        return $this->getSpecialRegionsAt($date)->contains('id', $region->id);
    }

    /**
     * Get the special regions that the country belongs to at a given date.
     */
    public function getSpecialRegionsAt(?Carbon $date = null): Collection
    {
        $checkDate = $date ?? Carbon::now();

        return $this->specialRegions()
            ->wherePivot('start_date', '<=', $checkDate->toDateString())
            ->where(fn ($query) => $query
                ->whereNull('world_country_in_special_region.end_date')
                ->orWhere('world_country_in_special_region.end_date', '>=', $checkDate->toDateString())
            )
            ->get();
    }

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): CountryFactory
    {
        return CountryFactory::new();
    }
}
