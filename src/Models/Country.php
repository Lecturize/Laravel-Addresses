<?php

namespace Lecturize\Addresses\Models;

use Illuminate\Database\Eloquent\Builder;
use Webpatser\Countries\Countries;

/**
 * Class Country
 * @package Lecturize\Addresses\Models
 *
 * @property-read int  $id
 *
 * @property string|null  $capital
 * @property string|null  $citizenship
 * @property string|null  $country_code
 * @property string|null  $currency
 * @property string|null  $currency_code
 * @property string|null  $currency_sub_unit
 * @property string|null  $full_name
 * @property string|null  $name
 * @property string|null  $iso_3166_2
 * @property string|null  $iso_3166_3
 * @property string|null  $region-code
 * @property string|null  $sub-region-code
 * @property string|null  $eea
 * @property string|null  $calling_code
 * @property string|null  $currency_symbol
 * @property string|null  $flag
 *
 * @method static Builder|Country whereId(int $value)
 * @method static Builder|Country whereCapital(string $value)
 * @method static Builder|Country whereCountryCode(string $country_code)
 * @method static Builder|Country whereCurrency(string $value)
 * @method static Builder|Country whereCurrencyCode(string $value)
 * @method static Builder|Country whereFullName(string $value)
 * @method static Builder|Country whereName(string $value)
 */
class Country extends Countries
{
    public function scopeWhereCountryCode(Builder $query, string $country_code): Builder
    {
        return $query->where('iso_3166_2',   $country_code)
                     ->orWhere('iso_3166_3', $country_code);
    }
}