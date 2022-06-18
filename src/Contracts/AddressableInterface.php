<?php

namespace Lecturize\Addresses\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface AddressableInterface
 * @package Lecturize\Addresses\Contracts
 */
interface AddressableInterface
{
    public function addresses(): MorphMany;
}