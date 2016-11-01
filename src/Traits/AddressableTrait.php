<?php namespace Lecturize\Addresses\Traits;

use Lecturize\Addresses\Exceptions\FailedValidationException;
use Lecturize\Addresses\Models\Address;

/**
 * Class AddressableTrait
 * @package Lecturize\Addresses\Traits
 * @deprecated Use HasAddresses trait instead.
 */
trait AddressableTrait
{
	use HasAddresses;
}