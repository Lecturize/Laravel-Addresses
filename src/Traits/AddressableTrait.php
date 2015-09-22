<?php namespace vendocrat\Addresses\Traits;

use vendocrat\Addresses\Exceptions\FailedValidationException;
use vendocrat\Addresses\Models\Address;

/**
 * Class AddressableTrait
 * @package vendocrat\Addresses\Traits
 */
trait AddressableTrait
{
	/**
	 * Get all addresses for this model.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function addresses() {
		return $this->morphMany(Address::class, 'addressable');
	}

	/**
	 * Check if model has an address.
	 *
	 * @return bool
	 */
	public function hasAddress()
	{
		return (bool) $this->addresses()->count();
	}

	/**
	 * Add an address to this model.
	 *
	 * @param  array $attributes
	 * @return Address
	 */
	public function addAddress( array $attributes )
	{
		$attributes = $this->loadAddressAttributes($attributes);

		return $this->addresses()->updateOrCreate($attributes);
	}

	/**
	 * Updates the given address.
	 *
	 * @param  Address $address
	 * @param  array   $attributes
	 * @return Address
	 */
	public function updateAddress( Address $address, array $attributes )
	{
		$attributes = $this->loadAddressAttributes($attributes);

		return $address->fill($attributes)->save();
	}

	/**
	 * Deletes given address.
	 *
	 * @param  Address $address
	 * @return bool
	 */
	public function deleteAddress( Address $address )
	{
		if ( $this != $address->addressable()->first() )
			return false;

		return $address->delete();
	}

	/**
	 * Deletes all the addresses of this model.
	 *
	 * @return bool
	 */
	public function flushAddresses()
	{
		return $this->addresses()->delete();
	}

	/**
	 * Get primary address
	 *
	 * @return Address|null
	 */
	public function getPrimaryAddress() {
		return $this->addresses()->orderBy('is_primary', 'DESC')->first();
	}

	/**
	 * Get billing address
	 *
	 * @return Address|null
	 */
	public function getBillingAddress() {
		return $this->addresses()->orderBy('is_billing', 'DESC')->first();
	}

	/**
	 * Get shipping address
	 *
	 * @return Address|null
	 */
	public function getShippingAddress() {
		return $this->addresses()->orderBy('is_shipping', 'DESC')->first();
	}

	/**
	 * Add country id to attributes array
	 *
	 * @param  array $attributes
	 * @return array $attributes
	 * @throws FailedValidationException
	 */
	public function loadAddressAttributes( array $attributes ) {
		// return if no country given
		if ( ! isset($attributes['country']) )
			return $attributes;

		// find country
		$country = \Countries::where('iso_3166_2', $attributes['country'])
			->orWhere('iso_3166_3', $attributes['country'])
			->first();

		// unset country from attributes array
		unset($attributes['country']);

		// add country_id to attributes array
		if ( is_object($country) && isset($country->id) )
			$attributes['country_id'] = $country->id;

		// run validation
		$validator = $this->validateAddress($attributes);

		if ( $validator->fails() )
			throw new FailedValidationException('Validator failed for: '. implode(', ', $attributes));

		// return attributes array with country_id key/value pair
		return $attributes;
	}

	/**
	 * Add country id to attributes array
	 *
	 * @param  array $attributes
	 * @return array $attributes
	 */
	function validateAddress( array $attributes ) {
		$rules = Address::getValidationRules();

		return \Validator::make($attributes, $rules);
	}
}