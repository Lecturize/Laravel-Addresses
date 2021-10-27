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
          'values' => 'contact_values'
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
     'value_flags' => ['public', 'primary', 'shipping', 'billing'],

     /*
         * The validation rules for a contact.
         */
     'rules' => [],

     /**
      * Tax column name
      */
     'tax_column' => "vat_id",

];
