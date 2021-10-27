<?php

return [
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
         */
        'flags' => ['public', 'primary'],

        /*
         * The validation rules for a contact.
         */
        'rules' => [],

        /**
         * Tax column name
         */
        'tax_column' => "vat_id",
    ],
];
