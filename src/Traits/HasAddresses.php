<?php

namespace Lecturize\Addresses\Traits;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

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
    /**
     * This method allows for dynamic method calls to retrieve addresses by their associated flags,
     * which can be useful for managing addresses with different purposes or locations
     *
     * @param string $method
     * @param array $parameters
     *
     */
    public function __call($method, $parameters)
    {
        $available_flags = config('lecturize.addresses.flags');
        $available_flags = array_map(function ($flag) {
            return Str::ucfirst($flag);
        }, $available_flags);

        if (preg_match('/^get(' . implode('|', $available_flags) . ')Address$/', $method, $matches)) {
            $flag = strtolower($matches[1]);

            return $this->getAddressByFlag($flag, $parameters[0] ?? 'desc');
        }

        return parent::__call($method, $parameters);
    }

    public function addresses(): MorphMany
    {
        /** @var Model $this */
        return $this->morphMany(Address::class, 'addressable');
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

    public function getAddressByFlag(?string $flag = null, string $direction = 'desc'): ?Address
    {
        if (! $this->hasAddresses()) {
            return null; // short circuit if no addresses exist
        }

        if ($flag !== null) {
            $search_flag = 'is_' . $flag;
            $address = $this->addresses()->where($search_flag, true)
                ->orderBy($search_flag, $direction)
                ->first();

            if ($address !== null) {
                return $address;
            }

            /**
             * use the array order of config lecturize.addresses.flags to build up
             * a fallback solution for when no address with the given flag exists
             */
            $fallback_order = config('lecturize.addresses.flags');

            /**
             * fallback order is an array of flags like: ['public', 'primary', 'billing', 'shipping']
             * when calling getBillingAddress() and no address with the billing flag exists, the next earliest flag is used
             * in this case, the flag 'primary' would be used
             */
            $current_flag_index = array_search($flag, $fallback_order);
            $try_flag = $fallback_order[$current_flag_index - 1] ?? null;

            if ($try_flag !== null) {
                $address = $this->getAddressByFlag($try_flag, $direction);

                if ($address !== null) {
                    return $address;
                }
            }
        }

        /**
         * should the default fallback logic fail, try to get the first or last address
         */
        if (! $address && $direction === 'desc') {
            return $this->addresses()->first();
        } elseif (! $address && $direction === 'asc') {
            return $this->addresses()->last();
        }

        return null;
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
        $rules = (new Address)->getValidationRules();

        return validator($attributes, $rules);
    }

    function findCountryByCode(string $country_code): ?Country
    {
        return Country::whereCountryCode($country_code)
                      ->first();
    }
}
