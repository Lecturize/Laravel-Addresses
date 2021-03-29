<?php namespace Lecturize\Addresses\Traits;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Collection;

use Lecturize\Addresses\Models\Address;
use Lecturize\Addresses\Exceptions\FailedValidationException;

use Webpatser\Countries\Countries;

/**
 * Class HasAddresses
 * @package Lecturize\Addresses\Traits
 * @property Collection  $addresses
 */
trait HasAddresses
{
    /**
     * Get all addresses for this model.
     *
     * @return MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Check if model has addresses.
     *
     * @return bool
     */
    public function hasAddresses(): bool
    {
        return (bool) count($this->addresses);
    }

    /**
     * Check if model has an address.
     * @deprecated Use hasAddresses() instead.
     *
     * @return bool
     */
    public function hasAddress(): bool
    {
        return $this->hasAddresses();
    }

    /**
     * Add an address to this model.
     *
     * @param  array  $attributes
     * @return mixed
     * @throws Exception
     */
    public function addAddress(array $attributes)
    {
        $attributes = $this->loadAddressAttributes($attributes);

        return $this->addresses()->updateOrCreate($attributes);
    }

    /**
     * Updates the given address.
     *
     * @param  Address  $address
     * @param  array    $attributes
     * @return bool
     * @throws Exception
     */
    public function updateAddress(Address $address, array $attributes): bool
    {
        $attributes = $this->loadAddressAttributes($attributes);

        return $address->fill($attributes)->save();
    }

    /**
     * Deletes given address.
     *
     * @param  Address  $address
     * @return bool
     * @throws Exception
     */
    public function deleteAddress(Address $address): bool
    {
        return $this->addresses()->where('id', $address->id)->delete();
    }

    /**
     * Deletes all the addresses of this model.
     *
     * @return bool
     */
    public function flushAddresses(): bool
    {
        return $this->addresses()->delete();
    }

    /**
     * Get the primary address.
     *
     * @param  string  $direction
     * @return Address|null
     */
    public function getPrimaryAddress($direction = 'desc'): ?Address
    {
        return $this->addresses()
                    ->primary()
                    ->orderBy('is_primary', $direction)
                    ->first();
    }

    /**
     * Get the billing address.
     *
     * @param  string  $direction
     * @return Address|null
     */
    public function getBillingAddress(string $direction = 'desc'): ?Address
    {
        return $this->addresses()
                    ->billing()
                    ->orderBy('is_billing', $direction)
                    ->first();
    }

    /**
     * Get the first shipping address.
     *
     * @param  string  $direction
     * @return Address|null
     */
    public function getShippingAddress(string $direction = 'desc'): ?Address
    {
        return $this->addresses()
                    ->shipping()
                    ->orderBy('is_shipping', $direction)
                    ->first();
    }

    /**
     * Add country id to attributes array.
     *
     * @param  array  $attributes
     * @return array
     * @throws FailedValidationException
     */
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

    /**
     * Validate the address.
     *
     * @param  array  $attributes
     * @return Validator
     */
    function validateAddress(array $attributes): Validator
    {
        $rules = (new Address)->getValidationRules();

        return validator($attributes, $rules);
    }

    /**
     * Validate the address.
     *
     * @param  string  $country_code
     * @return Countries|null
     */
    function findCountryByCode($country_code): ?Countries
    {
        return Countries::where('iso_3166_2',   $country_code)
                        ->orWhere('iso_3166_3', $country_code)
                        ->first();
    }
}