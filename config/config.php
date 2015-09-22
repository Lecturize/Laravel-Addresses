<?php
return [
	/*
	 * Table for contacts
	 */
	'table_contacts' => 'contacts',

	/*
	 * Table for addresses
	 */
	'table_addresses' => 'addresses',

	/*
	 * Flags for addresses
	 */
	'flags' => ['primary', 'billing', 'shipping'],

	/*
	 * Enable geocoding to add coordinates (lon/lat) to addresses
	 */
	'geocode' => true,
];
