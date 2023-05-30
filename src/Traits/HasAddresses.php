<?php

namespace Lecturize\Addresses\Traits;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Collection;

use Lecturize\Addresses\Models\Address;
use Lecturize\Addresses\Exceptions\FailedValidationException;
use Lecturize\Addresses\Models\Country;

/**
 * Class HasAddresses
 * @package Lecturize\Addresses\Traits
 * @property-read Collection|Address[]  $addresses
 */
trait HasAddresses
{
    public function addresses(): MorphMany
    {
        /** @var Model $this */
        return $this->morphMany(config('lecturize.addresses.model'), 'addressable');
    }

    public function hasAddresses(): bool
    {
        return $this->addresses->isNotEmpty();
    }

    /** @throws Exception */
    public function addAddress(array $attributes): Address|Model
    {
        $attributes = $this->loadAddressAttributes($attributes);

        return $this->addresses()->updateOrCreate($attributes);
    }

    /** @throws Exception */
    public function updateAddress(Address $address, array $attributes): bool
    {
        $attributes = $this->loadAddressAttributes($attributes);

        return $address->fill($attributes)->save();
    }

    /** @throws Exception */
    public function deleteAddress(Address $address): bool
    {
        return $this->addresses()->where('id', $address->id)->delete();
    }

    public function flushAddresses(): bool
    {
        return $this->addresses()->delete();
    }

    public function getPrimaryAddress(string $direction = 'desc'): ?Address
    {
        return $this->addresses()
                    ->primary()
                    ->orderBy('is_primary', $direction)
                    ->first();
    }

    public function getBillingAddress(string $direction = 'desc'): ?Address
    {
        return $this->addresses()
                    ->billing()
                    ->orderBy('is_billing', $direction)
                    ->first();
    }

    public function getShippingAddress(string $direction = 'desc'): ?Address
    {
        return $this->addresses()
                    ->shipping()
                    ->orderBy('is_shipping', $direction)
                    ->first();
    }

    /** @throws FailedValidationException */
    public function loadAddressAttributes(array $attributes): array
    {
        // return if no country given
        if (! isset($attributes['country']))
            throw new FailedValidationException('[Addresses] No country code given.');

        // find country
        if (! ($country = $this->findCountryByCode($attributes['country'])) || ! isset($country->id))
            throw new FailedValidationException('[Addresses] Country not found, did you seed the countries table?');

        // unset country from attributes array
        unset($attributes['country']);
        $attributes['country_id'] = $country->id;

        // run validation
        $validator = $this->validateAddress($attributes);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error  = '[Addresses] '. implode(' ', $errors);

            throw new FailedValidationException($error);
        }

        // return attributes array with country_id key/value pair
        return $attributes;
    }

    function validateAddress(array $attributes): Validator
    {
        $model = config('lecturize.addresses.model');
        $rules = (new $model)->getValidationRules();

        return validator($attributes, $rules);
    }

    function findCountryByCode(string $country_code): ?Country
    {
        return Country::whereCountryCode($country_code)
                      ->first();
    }
}
