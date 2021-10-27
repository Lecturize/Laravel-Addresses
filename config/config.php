<?php

return [
    /*
     * Contacts
     */

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
    'rules' => [],

    /**
     * Tax column name
     */
    'tax_column' => "vat_id",

];
