<?php

namespace Lecturize\Addresses\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

use Lecturize\Addresses\Models\Address;
use Lecturize\Addresses\Models\Contact;

/**
 * Class OwnsAddresses
 * @package Lecturize\Addresses\Traits
 * @property-read Collection|Address[]  $addresses
 * @property-read Collection|Contact[]  $contacts
 */
trait OwnsAddresses
{
    public function addresses(): HasMany
    {
        /** @var Model $this */
        return $this->hasMany(config('lecturize.addresses.model', Address::class));
    }

    public function contacts(): HasMany
    {
        /** @var Model $this */
        return $this->hasMany(config('lecturize.contacts.model', Contact::class));
    }

    /** @return Address[]|Collection */
    public function getBillingAddresses(): Collection|array
    {
        return $this->addresses()
                    ->billing()
                    ->get();
    }

    /** @return Address[]|Collection */
    public function getShippingAddresses(): Collection|array
    {
        return $this->addresses()
                    ->shipping()
                    ->get();
    }
}
