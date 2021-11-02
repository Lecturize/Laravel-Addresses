<?php

namespace Kwidoo\Contacts\Contracts;

use Kwidoo\Contacts\Collections\ContactItemCollection as Collection;

interface Item
{
    public function is(Item $value): bool;

    public static function newCollection($data): Collection;
}
