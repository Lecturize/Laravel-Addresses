<?php

return [
    /*
     * Addresses
     */
    'addresses' => [
        /*
         * Main table.
         */
        'table' => 'addresses',

        /*
         * The model used for addresses.
         */
        'model' => \Lecturize\Addresses\Models\Address::class,

        /*
         * Flag columns to be added to table.
         */
        'flags' => ['public', 'primary', 'billing', 'shipping'],

        /*
         * The validation rules for an address.
         */
        'rules' => [
            'street'       => 'required|string|min:3|max:60',
            'street_extra' => 'nullable|string|max:60',
            'city'         => 'required|string|min:3|max:60',
            'state'        => 'nullable|string|min:3|max:60',
            'post_code'    => 'required|min:4|max:10|AlphaDash',
            'country_id'   => 'required|integer',
        ],

        /*
         * Enable automatic geocoding to add coordinates (lon/lat) to addresses.
         * If you enable this option, please make sure to also add a
         * Google Maps API Key to your services' config file.
         * The key used is 'services.google.maps.key'.
         * Default: false
         */
        'geocode' => false,
    ],

    /*
     * Contacts
     */
    'contacts' => [
        /*
         * Main table.
         */
        'table' => 'contacts',

        /*
         * The model used for contacts.
         */
        'model' => \Lecturize\Addresses\Models\Contact::class,

        /*
         * Flag columns to be added to table.
         */
        'flags' => ['public', 'primary'],

        /*
         * The validation rules for a contact.
         */
        'rules' => [],
    ],
];
