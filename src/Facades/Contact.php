<?php

namespace Kwidoo\Contacts\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Address
 * @package Kwidoo\Contacts\Facades
 */
class Contact extends Facade
{
    /** @inheritdoc */
    protected static function getFacadeAccessor(): string
    {
        return 'contact';
    }
}
