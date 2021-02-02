<?php namespace Lecturize\Addresses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Lecturize\Addresses\Traits\HasCountry;

/**
 * Class Address
 * @package Lecturize\Addresses\Models
 */
class Address extends Model
{
    use HasCountry;
    use SoftDeletes;

    /** @inheritdoc */
    protected $fillable = [
        'street',
        'street_extra',
        'city',
        'state',
        'post_code',
        'country_id',

        'notes',
        'properties',

        'lat',
        'lng',

        'addressable_id',
        'addressable_type',
    ];

    /** @inheritdoc */
    protected $dates = [
        'deleted_at',
    ];

    /** @inheritdoc */
    protected $casts = [
        'properties' => 'array',
    ];

    /** @inheritdoc */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('lecturize.addresses.table', 'addresses');
        $this->updateFillables();
    }

    /** @inheritdoc */
    public static function boot()
    {
        parent::boot();

        static::saving(function($address) {
            if (config('lecturize.addresses.geocode', false))
                $address->geocode();
        });
    }

    /**
     * Update fillable fields dynamically.
     *
     * @return void.
     */
    private function updateFillables()
    {
        $fillable = $this->fillable;
        $columns  = preg_filter('/^/', 'is_', config('lecturize.addresses.columns', ['public', 'primary', 'billing', 'shipping']));

        $this->fillable(array_merge($fillable, $columns));
    }

    /**
     * Get the related model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public static function getValidationRules()
    {
        $rules = [
            'street'       => 'required|string|min:3|max:60',
            'street_extra' => 'nullable|string|min:3|max:60',
            'city'         => 'required|string|min:3|max:60',
            'state'        => 'nullable|string|min:3|max:60',
            'post_code'    => 'required|min:4|max:10|AlphaDash',
            'country_id'   => 'required|integer',
        ];

        foreach(config('lecturize.addresses.flags', ['public', 'primary', 'billing', 'shipping']) as $flag)
            $rules['is_'.$flag] = 'boolean';

        return $rules;
    }

    /**
     * Try to fetch the coordinates from Google and store them.
     *
     * @return $this
     */
    public function geocode()
    {
        if (! ($query = $this->getQueryString()))
            return $this;

        $url = 'https://maps.google.com/maps/api/geocode/json?address='. $query .'&sensor=false';

        if ($geocode = file_get_contents($url)) {
            $output = json_decode($geocode);

            if (count($output->results) && isset($output->results[0])) {
                if ($geo = $output->results[0]->geometry) {
                    $this->lat = $geo->location->lat;
                    $this->lng = $geo->location->lng;
                }
            }
        }

        return $this;
    }

    /**
     * Get the encoded query string.
     *
     * @return string
     */
    public function getQueryString()
    {
        $query = [];
        $query[] = $this->street       ?: '';
    //  $query[] = $this->street_extra ?: '';
        $query[] = $this->city         ?: '';
        $query[] = $this->state        ?: '';
        $query[] = $this->post_code    ?: '';
        $query[] = $this->country_name ?: '';

        $query = trim(implode(',', array_filter($query)));

        return urlencode($query);
    }

    /**
     * Get the address as array.
     *
     * @return array
     */
    public function getArray()
    {
        $address = $two = [];

        $two[] = $this->post_code ?: '';
        $two[] = $this->city      ?: '';
        $two[] = $this->state     ? '('. $this->state .')' : '';

        $address[] = $this->street       ?: '';
        $address[] = $this->street_extra ?: '';
        $address[] = implode(' ', array_filter($two));
        $address[] = $this->country_name ?: '';

        if (count($address = array_filter($address)) > 0)
            return $address;

        return null;
    }

    /**
     * Get the address as html block.
     *
     * @return string
     */
    public function getHtml()
    {
        if ($address = $this->getArray())
            return '<address>'. implode('<br />', array_filter($address)) .'</address>';

        return null;
    }

    /**
     * Get the address as a simple line.
     *
     * @param  string  $glue
     * @return string
     */
    public function getLine($glue = ', ')
    {
        if ($address = $this->getArray())
            return implode($glue, array_filter($address));

        return null;
    }

    /**
     * Get the country name.
     * @deprecated Unexpected behaviour (would expect $address->country()->get()), use country_name attribute instead.
     *
     * @return string
     */
    public function getCountry()
    {
        if ($this->country && $country = $this->country->name)
            return $country;

        return '';
    }

    /**
     * Get the country name.
     *
     * @return string
     */
    public function getCountryNameAttribute()
    {
        if ($this->country)
            return $this->country->name;

        return '';
    }

    /**
     * Get the country code.
     *
     * @param  int  $digits
     * @return string
     */
    public function getCountryCodeAttribute($digits = 2)
    {
        if (! $this->country)
            return '';

        if ($digits === 3)
            return $this->country->iso_3166_3;

        return $this->country->iso_3166_2;
    }

    /**
     * Get the route name (without street number).
     *
     * @return string
     */
    public function getRouteAttribute()
    {
        if (preg_match('/([^\d]+)\s?(.+)/i', $this->street, $result))
            return $result[1];

        return '';
    }

    /**
     * Get the street number.
     *
     * @return string
     */
    public function getStreetNumberAttribute()
    {
        if (preg_match('/([^\d]+)\s?(.+)/i', $this->street, $result))
            return $result[2];

        return '';
    }

    /**
     * Scope primary addresses
     *
     * @param  object  $query
     * @return mixed
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope billing addresses
     *
     * @param  object  $query
     * @return mixed
     */
    public function scopeBilling($query)
    {
        return $query->where('is_billing', true);
    }

    /**
     * Scope shipping addresses
     *
     * @param  object  $query
     * @return mixed
     */
    public function scopeShipping($query)
    {
        return $query->where('is_shipping', true);
    }
}