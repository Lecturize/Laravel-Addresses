<?php

namespace Lecturize\Addresses\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Address
 * @package Lecturize\Addresses\Facades
 */
class Address extends Facade
{
    /** @inheritdoc */
    protected static function getFacadeAccessor(): string
    {
        return 'address';
    }
}