<?php namespace Lecturize\Addresses\Facades;

use Illuminate\Support\Facades\Facade;

class Addresses extends Facade
{
	/**
     * @inheritdoc
	 */
	protected static function getFacadeAccessor()
	{
		return 'addresses';
	}
}