<?php namespace Lecturize\Addresses\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
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
}