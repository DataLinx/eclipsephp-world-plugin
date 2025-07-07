<?php

namespace Eclipse\World\Models;

use Eclipse\World\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'world_posts';

    protected $fillable = [
        'country_id',
        'code',
        'name',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    /**
     * Get validation rules for the model
     */
    public static function getValidationRules(?self $record = null): array
    {
        return [
            'country_id' => ['required', 'string', 'max:2', 'exists:world_countries,id'],
            'code' => [
                'required',
                'string',
                Rule::unique('world_posts', 'code')
                    ->where('country_id', request('country_id'))
                    ->ignore($record?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
