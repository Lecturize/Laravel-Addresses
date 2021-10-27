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

    public function __construct($data)
    {
        $this->data = json_decode($data);
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
        return null;
    }
}
