<?php

namespace Kwidoo\Contacts\Items;

use Illuminate\Support\Str;
use Kwidoo\Contacts\Models\Contact;

class ContactItem
{
    /**
     * @var object
     */
    protected $data;

    /**
     * @param object|string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
        if (is_string($data)) {
            $this->data = json_decode($data);
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
        if (isset($this->data->$name)) {
            return $this->data->$name;
        }
        return $this->defaults($name);
    }

    /**
     * @param mixed $name
     *
     * @return mixed|null
     */
    protected function defaults($name)
    {
        $defs = ['type' => 'email', 'value' => null]; //@todo move to config
        if (!in_array($name, $defs)) {
            return null;
        }
        return $defs[$name];
    }

    public static function newCollection($data)
    {
        if (is_string($data)) {
            $data = json_decode($data);
        }

        return collect($data)->map(function ($item) {
            return new self($item);
        });
    }
}
