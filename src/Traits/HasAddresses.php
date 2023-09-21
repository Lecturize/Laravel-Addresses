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
        return $this->morphMany(config('lecturize.addresses.model', Address::class), 'addressable');
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

    public function getAddress(string $flag = null, string $direction = 'desc', bool $strict = false): ?Address
    {
        if (! $this->hasAddresses()) {
            return null; // short circuit if no addresses exist
        }

        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        if ($flag !== null) {
            $address = $this->addresses()
                ->flag($flag, true)
                ->orderBy('is_' . $flag, $direction)
                ->first();

            if ($address !== null) {
                return $address;
            }

            if ($strict) {
                return null;
            }

            /**
             * use the array order of config lecturize.addresses.flags to build up
             * a fallback solution for when no address with the given flag exists
             */
            $fallback_order = config('lecturize.addresses.flags', []);

            /**
             * fallback order is an array of flags like: ['public', 'primary', 'billing', 'shipping']
             * when calling getAddress('billing') and no address with the billing flag exists, the next earliest flag is used
             * in this case, the flag 'primary' would be used
             */
            $current_flag_index = array_search($flag, $fallback_order);
            $try_flag = $fallback_order[$current_flag_index - 1] ?? null;

            if ($try_flag !== null) {
                $address = $this->getAddress($try_flag, $direction);

                if ($address !== null) {
                    return $address;
                }
            }
        }

        /**
         * should the default fallback logic fail, try to get the first or last address
         */
        if (! $address && $direction === 'DESC') {
            return $this->addresses()->first();
        } elseif (! $address && $direction === 'ASC') {
            return $this->addresses()->last();
        }

        return null;
    }

    /** @deprecated use getAddress('primary', $direction) instead */
    public function getPrimaryAddress(string $direction = 'desc'): ?Address
    {
        return $this->getAddress('primary', $direction, true);
    }

    /** @deprecated use getAddress('billing', $direction) instead */
    public function getBillingAddress(string $direction = 'desc'): ?Address
    {
        return $this->getAddress('billing', $direction, true);
    }

    /** @deprecated use getAddress('shipping', $direction) instead */
    public function getShippingAddress(string $direction = 'desc'): ?Address
    {
        return $this->getAddress('shipping', $direction, true);
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
        $model = config('lecturize.addresses.model', Address::class);
        $rules = (new $model)->getValidationRules();

        return validator($attributes, $rules);
    }

    function findCountryByCode(string $country_code): ?Country
    {
        return Country::whereCountryCode($country_code)
                      ->first();
    }
}
