<?php

namespace Kwidoo\Contacts\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

use Kwidoo\Contacts\Models\Address;
use Kwidoo\Contacts\Models\Contact;

/**
 * Class OwnsAddresses
 * @package Kwidoo\Contacts\Traits
 * @property Collection|Address[]  $addresses
 * @property Collection|Contact[]  $contacts
 */
trait OwnsContact
{
    /**
     * Get all contacts this model owns.
     *
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
