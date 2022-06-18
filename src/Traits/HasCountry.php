<?php

namespace Lecturize\Addresses\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Lecturize\Addresses\Models\Country;

/**
 * Class HasCountry
 * @package Lecturize\Addresses\Traits;
 *
 * @property int|null  $country_id
 * @property string    $country_code
 *
 * @property-read Country|null  $country
 *
 * @method static Builder|Model byCountry(string $value)
 */
trait HasCountry
{
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function getCountryCodeAttribute(): string
    {
        if ($country = $this->country)
            return $country->iso_3166_2;

        return '';
    }

    public function scopeByCountry(Builder $query, Country|int $country): Builder
    {
        $country = is_int($country) ? $country : $country->id;

        return $query->whereHas('country', function(Builder $q) use($country) {
            $q->where('id', $country);
        });
    }
}