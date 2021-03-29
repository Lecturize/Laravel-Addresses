<?php namespace Lecturize\Addresses\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use Lecturize\Addresses\Traits\HasCountry;

/**
 * Class Address
 * @package Lecturize\Addresses\Models
 * @property string|null  $street
 * @property string|null  $street_extra
 * @property string|null  $city
 * @property string|null  $state
 * @property string|null  $post_code
 * @property string|null  $country_name
 * @property string|null  $notes
 * @property array|null   $properties
 * @property string|null  $lat
 * @property string|null  $lng
 * @property Model|null   $addressable
 * @property Collection   $contacts
 * @property Model|null   $user
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

        'addressable_type',
        'addressable_id',
        'user_id',
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

        static::creating(function ($model) {
            if ($model->getConnection()
                      ->getSchemaBuilder()
                      ->hasColumn($model->getTable(), 'uuid'))
                $model->uuid = \Webpatser\Uuid\Uuid::generate()->string;
        });

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
     * @return MorphTo
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the related contacts.
     *
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get the user this address belongs to.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('lecturize.addresses.users.model', config('auth.providers.users.model', 'App\Models\Users\User')));
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public static function getValidationRules(): array
    {
        $rules = config('lecturize.addresses.rules', [
            'street'       => 'required|string|min:3|max:60',
            'street_extra' => 'nullable|string|min:3|max:60',
            'city'         => 'required|string|min:3|max:60',
            'state'        => 'nullable|string|min:3|max:60',
            'post_code'    => 'required|min:4|max:10|AlphaDash',
            'country_id'   => 'required|integer',
        ]);

        foreach (config('lecturize.addresses.flags', ['public', 'primary', 'billing', 'shipping']) as $flag)
            $rules['is_'.$flag] = 'boolean';

        return $rules;
    }

    /**
     * Try to fetch the coordinates from Google and store them.
     *
     * @return $this
     */
    public function geocode(): self
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
    public function getQueryString(): string
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
    public function getArray(): array
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

        return [];
    }

    /**
     * Get the address as html block.
     *
     * @return string
     */
    public function getHtml(): string
    {
        if ($address = $this->getArray())
            return '<address>'. implode('<br />', array_filter($address)) .'</address>';

        return '';
    }

    /**
     * Get the address as a simple line.
     *
     * @param  string  $glue
     * @return string
     */
    public function getLine($glue = ', '): string
    {
        if ($address = $this->getArray())
            return implode($glue, array_filter($address));

        return '';
    }

    /**
     * Get the country name.
     * @deprecated Unexpected behaviour (would expect $address->country()->get()), use country_name attribute instead.
     *
     * @return string
     */
    public function getCountry(): string
    {
        if ($this->country && ($country = $this->country->name))
            return $country;

        return '';
    }

    /**
     * Get the country name.
     *
     * @return string
     */
    public function getCountryNameAttribute(): string
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
    public function getCountryCodeAttribute($digits = 2): string
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
    public function getRouteAttribute(): string
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
    public function getStreetNumberAttribute(): string
    {
        if (preg_match('/([^\d]+)\s?(.+)/i', $this->street, $result))
            return $result[2];

        return '';
    }

    /**
     * Scope primary addresses
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope billing addresses
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeBilling(Builder $query): Builder
    {
        return $query->where('is_billing', true);
    }

    /**
     * Scope shipping addresses
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeShipping(Builder $query): Builder
    {
        return $query->where('is_shipping', true);
    }
}