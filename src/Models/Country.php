<?php

namespace Eclipse\World\Models;

use Eclipse\World\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    protected static function newFactory(): CountryFactory
    {
        return CountryFactory::new();
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function specialRegions()
    {
        return $this->belongsToMany(Region::class, 'world_country_in_special_region', 'country_id', 'region_id')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }

    /**
    * Check if country belongs to special region (with dates considered)
    */
    public function inSpecialRegion(string $code): bool
    {
        $now = now();

        return $this->specialRegions()
            ->where('code', $code)
            ->where('start_date', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->exists();
    }
}
