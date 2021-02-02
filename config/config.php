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
         * Flag columns to be added to table.
         * Custom flags might be removed in future versions
         * in favor of the properties attribute.
         */
        'flags' => ['public', 'primary', 'billing', 'shipping'],

        /*
         * Enable geocoding to add coordinates (lon/lat) to addresses.
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
         * Flag columns to be added to table.
         * Custom flags might be removed in future versions
         * in favor of the properties attribute.
         */
        'flags' => ['public', 'primary'],
    ],
];