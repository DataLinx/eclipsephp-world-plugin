<?php

namespace Eclipse\World\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CountryInSpecialRegion extends Pivot
{
    protected $table = 'world_country_in_special_region';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $fillable = [
        'country_id',
        'region_id',
        'start_date',
        'end_date',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
