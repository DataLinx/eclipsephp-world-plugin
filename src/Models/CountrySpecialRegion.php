<?php

namespace Eclipse\World\Models;

use Eclipse\World\Factories\CountrySpecialRegionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountrySpecialRegion extends Model
{
    use HasFactory;

    protected $table = 'world_country_in_special_region';

    protected $fillable = [
        'country_id',
        'region_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the country that the country belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the region that the country belongs to.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Check if the country belongs to a special region at a given date.
     */
    public function isActive(?string $date = null): bool
    {
        $checkDate = $date ?? now()->toDateString();

        return $this->start_date <= $checkDate &&
               ($this->end_date === null || $this->end_date >= $checkDate);
    }

    /**
     * Scope a query to only include active country special regions.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive($query, ?string $date = null)
    {
        $checkDate = $date ?? now()->toDateString();

        return $query->where('start_date', '<=', $checkDate)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $checkDate));
    }

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): CountrySpecialRegionFactory
    {
        return CountrySpecialRegionFactory::new();
    }
}
