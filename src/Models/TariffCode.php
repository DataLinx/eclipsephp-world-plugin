<?php

namespace Eclipse\World\Models;

use Eclipse\World\Factories\TariffCodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TariffCode extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'world_tariff_codes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'year',
        'code',
        'name',
        'measure_unit',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'year' => 'integer',
        'name' => 'array',
        'measure_unit' => 'array',
    ];

    /**
     * The attributes that are translatable.
     */
    public array $translatable = [
        'name',
        'measure_unit',
    ];

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): TariffCodeFactory
    {
        return TariffCodeFactory::new();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tariffCode) {
            if (empty($tariffCode->year)) {
                $tariffCode->year = (int) date('Y');
            }
        });
    }
}
