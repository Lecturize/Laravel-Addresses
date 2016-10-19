<?php

return [
    /*
     * Contacts
     */
    'contacts' => [
        /*
         * Main table
         */
        'table' => 'contacts',

        /*
         * Flag columns to be added to table
         */
        'flags' => ['public', 'primary'],
    ],

    /*
     * Contacts
     */
    'addresses' => [
        /*
         * Main table
         */
        'table'   => 'addresses',

        /*
         * Flag columns to be added to table
         */
        'flags'   => ['public', 'primary', 'billing', 'shipping'],

        /*
         * Enable geocoding to add coordinates (lon/lat) to addresses
         */
        'geocode' => true,
    ],
];
