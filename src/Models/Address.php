<?php

namespace Lecturize\Addresses\Models;

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
 *
 * @property-read int  $id
 *
 * @property string|null  $street
 * @property string|null  $street_extra
 * @property string|null  $city
 * @property string|null  $state
 * @property string|null  $post_code
 * @property string|null  $notes
 * @property array|null   $properties
 * @property string|null  $lat
 * @property string|null  $lng
 *
 * @property-read string  $country_name
 * @property-read string  $country_code
 * @property-read string  $route
 * @property-read string  $street_number
 *
 * @property-read Model|null            $addressable
 * @property-read Collection|Contact[]  $contacts
 * @property-read Model|null            $user
 *
 * @method static Builder|Address primary()
 * @method static Builder|Address billing()
 * @method static Builder|Address shipping()
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
    protected $casts = [
        'properties' => 'array',

        'deleted_at' => 'datetime',
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

    private function updateFillables(): void
    {
        $fillable = $this->fillable;
        $columns  = preg_filter('/^/', 'is_', config('lecturize.addresses.columns', ['public', 'primary', 'billing', 'shipping']));

        $this->fillable(array_merge($fillable, $columns));
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('lecturize.addresses.users.model', config('auth.providers.users.model', 'App\Models\Users\User')));
    }

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

    public function getHtml(): string
    {
        if ($address = $this->getArray())
            return '<address>'. implode('<br />', array_filter($address)) .'</address>';

        return '';
    }

    public function getLine(string $glue = ', '): string
    {
        if ($address = $this->getArray())
            return implode($glue, array_filter($address));

        return '';
    }

    public function getCountryNameAttribute(): string
    {
        if ($this->country)
            return $this->country->name;

        return '';
    }

    public function getCountryCodeAttribute(?int $digits = 2): string
    {
        if (! $this->country)
            return '';

        if ($digits === 3)
            return $this->country->iso_3166_3;

        return $this->country->iso_3166_2;
    }

    public function getRouteAttribute(): string
    {
        if (preg_match('/(\D+)\s?(.+)/i', $this->street, $result))
            return $result[1];

        return '';
    }

    public function getStreetNumberAttribute(): string
    {
        if (preg_match('/(\D+)\s?(.+)/i', $this->street, $result))
            return $result[2];

        return '';
    }

    public function scopeFlag(Builder $query, string $flag): Builder
    {
        return $query->where('is_'.$flag, true);
    }

    /** @deprecated use scopeFlag('primary') instead */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    /** @deprecated use scopeFlag('billing') instead */
    public function scopeBilling(Builder $query): Builder
    {
        return $query->where('is_billing', true);
    }

    /** @deprecated use scopeFlag('shipping') instead */
    public function scopeShipping(Builder $query): Builder
    {
        return $query->where('is_shipping', true);
    }
}
