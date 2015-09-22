<?php namespace vendocrat\Addresses\Traits;

use vendocrat\Addresses\Models\Address;

trait AddressableTrait
{
	/**
	 * Get all addresses for this model.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function addresses() {
		return $this->hasMany(Address::class);
	}

	/**
	 * Fetch primary address
	 *
	 * @return Address|null
	 */
	public function primaryAddress() {
		return $this->addresses()->orderBy('is_primary', 'DESC')->first();
	}

	/**
	 * Fetch billing address
	 *
	 * @return Address|null
	 */
	public function billingAddress() {
		return $this->addresses()->orderBy('is_billing', 'DESC')->first();
	}

	/**
	 * Fetch billing address
	 *
	 * @return Address|null
	 */
	public function shippingAddress() {
		return $this->addresses()->orderBy('is_shipping', 'DESC')->first();
	}
}