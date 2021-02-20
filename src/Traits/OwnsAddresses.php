<?php namespace Lecturize\Addresses\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Lecturize\Addresses\Models\Address;

/**
 * Class OwnsAddresses
 * @package Lecturize\Addresses\Traits
 */
trait OwnsAddresses
{
    /**
     * Get all addresses for this model.
     *
     * @return HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get all billing addresses for this model.
     *
     * @return Address[]|Collection
     */
    public function getBillingAddresses()
    {
        return $this->addresses()->where('is_billing', true)->get();
    }

    /**
     * Get all shipping addresses for this model.
     *
     * @return Address[]|Collection
     */
    public function getShippingAddresses()
    {
        return $this->addresses()->where('is_shipping', true)->get();
    }
}