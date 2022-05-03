<?php

namespace Kwidoo\Contacts\Items;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Kwidoo\Contacts\Collections\ContactItemCollection as Collection;
use Kwidoo\Contacts\Contracts\Item;
use Webpatser\Uuid\Uuid;
use JsonSerializable;
use stdClass;

/**
 * @package Kwidoo\Contacts\Items
 *
 * @property string $type
 * @property string $value
 *
 * @todo make complex validation: ex. no type if email,phone etc field exists
 * @todo make validation compatible with request
 */
class ContactItem implements Item, JsonSerializable
{
    /**
     * @var object
     */
    protected $data;

    /**
     * @var string[]
     */
    protected array $rules = [];

    /**
     * @param array|object|string $data
     */
    public function __construct($data = null, $rules = [])
    {
        $this->rules($rules);
        if (empty($rules)) {
            $this->setRulesFromConfig();
        }
        $this->data = new stdClass;
        if ($data !== null) {
            if (is_array($data)) {
                $data = (object)$data;
            }
            if (is_string($data)) {
                $data = json_decode($data);
            }
            $this->validate($data);
            $this->data = $data;
        }
        if (!isset($this->data->uuid)) {
            $this->data->uuid = Uuid::generate()->string;
        }
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        $method = 'get' . Str::title($name) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        // return if
        if (isset($this->data->type) && $this->data->type === $name) {
            return $this->data->value ?? null;
        }

        if (isset($this->data->$name)) {
            return $this->data->$name;
        }
        return null;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __set($name, $value)
    {
        $method = 'set' . Str::title($name) . 'Attribute';

        //@todo validation

        if (method_exists($this, $method)) {
            $this->$method($value);
            return;
        }

        if (in_array($name, config('contacts.value_types', ['email']))) {
            $this->data->type = $name;
            $this->data->value = $value;
            return;
        }

        if (isset($this->data->type) && $this->data->type === $name) {
            $this->data->value = $value;
            return;
        }
        $this->data->$name = $value;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    public function is(Item $value): bool
    {
        if (!isset($this->data->type) || !isset($this->data->value)) {
            return false;
        }
        return $this->data->type === $value->type && $this->data->value === $value->value;
    }

    public function rules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param string|object $data
     *
     * @return Collection
     */
    public static function newCollection($data): Collection
    {
        if (is_string($data)) {
            $data = json_decode($data);
        }

        return (new Collection($data))->map(function ($item) {
            return new self($item);
        });
    }

    /**
     * @param mixed $data
     *
     * @return void
     *
     * @throws ValidationException
     */
    protected function validate($data): void
    {
        $validator = Validator::make((array)$data, $this->rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * @return void
     */
    protected function setRulesFromConfig(): void
    {
        $this->rules = config('contacts.rules');
    }
}
