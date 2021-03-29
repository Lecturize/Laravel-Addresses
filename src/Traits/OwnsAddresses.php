<?php namespace Lecturize\Addresses\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

use Lecturize\Addresses\Models\Address;

/**
 * Class OwnsAddresses
 * @package Lecturize\Addresses\Traits
 * @property Collection  $addresses
 */
trait OwnsAddresses
{
    /**
     * Get all addresses for this model.
     *
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get all billing addresses for this model.
     *
     * @return Address[]|Collection
     */
    public function getBillingAddresses(): Collection
    {
        return $this->addresses()
                    ->where('is_billing', true)
                    ->get();
    }

    /**
     * Get all shipping addresses for this model.
     *
     * @return Address[]|Collection
     */
    public function getShippingAddresses(): Collection
    {
        return $this->addresses()
                    ->where('is_shipping', true)
                    ->get();
    }
}