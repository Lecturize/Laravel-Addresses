<?php

namespace Lecturize\Addresses\Facades;

use Illuminate\Support\Facades\Facade;

class Addresses extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'addresses';
    }
}
