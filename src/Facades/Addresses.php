<?php namespace Lecturize\Addresses\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Addresses
 * @package Lecturize\Addresses\Facades
 */
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