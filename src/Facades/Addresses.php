<?php namespace vendocrat\Addresses\Facades;

use Illuminate\Support\Facades\Facade;
use vendocrat\Addresses\Addresses as AddressesAccessor;

class Addresses extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return AddressesAccessor::class;
	}
}