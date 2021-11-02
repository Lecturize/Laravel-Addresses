<?php

namespace Kwidoo\Contacts\Collections;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Kwidoo\Contacts\Contracts\Item;

class ContactItemCollection extends Collection
{
    /**
     * Push one or more items onto the end of the collection.
     *
     * @param  mixed  $values
     * @return $this
     */
    public function push($value)
    {
        if ($value instanceof Item) {
            foreach ($this->items as $item) {
                if ($item->is($value)) {
                    return $this;
                }
            }
        }
        return parent::push($value);
    }

    /**
     * Find Item by value
     *
     * @param string|Item $value
     *
     * @return Item|null
     */
    public function find($value): ?Item
    {
        $item = $this->findWithKey($value);
        if (!empty($item)) {
            return array_values($item)[0];
        }
        return null;
    }

    /**
     * @param mixed $value
     *
     * @return Item[]
     */
    public function findWithKey($value): array
    {
        if ($value instanceof Item) {
            foreach ($this->items as $key => $item) {
                if ($item->is($value)) {
                    return [$key => $item];
                }
            }
        }
        if ($this->is_uuid($value)) {
            foreach ($this->items as $key => $item) {
                if ($item->uuid === $value) {
                    return [$key => $item];
                }
            }
        }
        if (is_string($value)) {
            foreach ($this->items as $key => $item) {
                if ($item->value === $value) {
                    return [$key => $item];
                }
            }
        }
        return [];
    }

    /**
     * @param string $uuid
     *
     * @return bool
     */
    protected function is_uuid($uuid): bool
    {
        if (!is_string($uuid)) {
            return false;
        }
        $validator = Validator::make(['uuid' => $uuid], ['uuid' => 'uuid']);
        return !$validator->fails();
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function ofType(string $type): self
    {
        return $this->filter(function (Item $item) use ($type) {
            return $item->type == $type;
        });
    }

    public function fixTree(): self
    {
        return $this->filter(function (Item $item) {
            var_dump($item); // . "\n";
            var_dump($item->value); // . "\n";
            var_dump(isset($item->type)); // && isset($item->value) && $item->type !== null && $item->value !== null);
            return true;
        });
    }
}
