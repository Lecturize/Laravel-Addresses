<?php

return [
    /*
     * Tables
     */
    'tables' => [
        'main' => 'contacts',
    ],

    /**
     * Possible owner types
     */
    'types' => ['personal', 'business'],

    /**
     * Possible contact types
     */
    'value_types' => ['phone', 'mobile', 'fax', 'email', 'address', 'website', 'facebook', 'twitter'],

    /*
         * Flag columns to be added to table.
         */
    'flags' => [],

    /*
         * The validation rules for a contact.
         */
    'rules' => [
        'type' => 'required|in:email,phone, mobile, fax, website, facebook, twitter',
        'value' => 'required|string',
    ],

    /**
     * Tax column name
     */
    'tax_column' => "vat_id",
];
