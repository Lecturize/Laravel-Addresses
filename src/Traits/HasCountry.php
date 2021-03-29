<?php namespace Lecturize\Addresses\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Webpatser\Countries\Countries;

/**
 * Class HasCountry
 * @package Lecturize\Addresses\Traits;
 * @property int|null        $country_id
 * @property Countries|null  $country
 */
trait HasCountry
{
    /**
     * Get the models country.
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Countries::class);
    }

    /**
     * Scope by country.
     *
     * @param  Builder  $query
     * @param  int      $id
     * @return Builder
     */
    public function scopeByCountry(Builder $query, int $id): Builder
    {
        return $query->whereHas('country', function($q) use($id) {
            $q->where('id', $id);
        });
    }
}