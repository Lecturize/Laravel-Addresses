[![Latest Stable Version](https://poser.pugx.org/lecturize/laravel-addresses/v/stable)](https://packagist.org/packages/lecturize/laravel-addresses)
[![Total Downloads](https://poser.pugx.org/lecturize/laravel-addresses/downloads)](https://packagist.org/packages/lecturize/laravel-addresses)
[![License](https://poser.pugx.org/lecturize/laravel-addresses/license)](https://packagist.org/packages/lecturize/laravel-addresses)

# Laravel Addresses

Simple address and contact management for Laravel with automatical geocoding to add longitude and latitude. Uses the famous [Countries](https://github.com/webpatser/laravel-countries) package by Webpatser.

## Installation

Require the package from your `composer.json` file

```php
"require": {
	"lecturize/laravel-addresses": "^1.1"
}
```

and run `$ composer update` or both in one with `$ composer require lecturize/laravel-addresses`.

## Configuration & Migration

```bash
$ php artisan vendor:publish --provider="Webpatser\Countries\CountriesServiceProvider"
$ php artisan vendor:publish --provider="Lecturize\Addresses\AddressesServiceProvider"
```

This will publish a `config/countries.php`, a `config/lecturize.php` and some migration files, that you'll have to run:

```bash
$ php artisan countries:migration
$ php artisan migrate
```

For migrations to be properly published ensure that you have added the directory `database/migrations` to the classmap in your projects `composer.json`.

Check out [Webpatser\Countries](https://github.com/webpatser/laravel-countries) readme to see how to seed their countries data to your database.

## Usage

First, add our `HasAddresses` trait to your model.
        
```php
<?php namespace App\Models;

use Lecturize\Addresses\Traits\HasAddresses;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasAddresses;

    // ...
}
?>
```

##### Add an Address to a Model
```php
$post = Post::find(1);
$post->addAddress([
    'street'     => '123 Example Drive',
    'city'       => 'Vienna',
    'post_code'  => '1110',
    'country'    => 'AT', // ISO-3166-2 or ISO-3166-3 country code
    'is_primary' => true, // optional flag
]);
```

Alternativly you could do...

```php
$address = [
    'street'     => '123 Example Drive',
    'city'       => 'Vienna',
    'post_code'  => '1110',
    'country'    => 'AT', // ISO-3166-2 or ISO-3166-3 country code
    'is_primary' => true, // optional flag
];
$post->addAddress($address);
```

Available attributes are `street`, `street_extra`, `city`, `post_code`, `state`, `country`, `state`, `notes` (for internal use). You can also use custom flags like `is_primary`, `is_billing` & `is_shipping`. Optionally you could also pass `lng` and `lat`, in case you deactivated the included geocoding functionality and want to add them yourself.

##### Check if Model has Addresses
```php
if ($post->hasAddresses()) {
    // Do something
}
```

##### Get all Addresses for a Model
```php
$addresses = $post->addresses()->get();
```

##### Get primary/billing/shipping Addresses
```php
$address = $post->getPrimaryAddress();
$address = $post->getBillingAddress();
$address = $post->getShippingAddress();
```

##### Update an Address for a Model
```php
$address = $post->addresses()->first(); // fetch the address

$post->updateAddress($address, $new_attributes);
```

##### Delete an Address from a Model
```php
$address = $post->addresses()->first(); // fetch the address

$post->deleteAddress($address); // delete by passing it as argument
```

##### Delete all Addresses from a Model
```php
$post->flushAddresses();
```

## Contacts

First, add our `HasContacts` trait to your model.

```php
<?php namespace App\Models;

use Lecturize\Addresses\Traits\HasContacts;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasContacts;

    // ...
}
?>
```

##### Add a Contact to a Model

```php
$post = Team::find(1);
$post->addContact([
    'first_name' => 'Alex',
    'website'    => 'https://twitter.com/AMPoellmann',
    'is_primary' => true, // optional flag
]);
```

##### Relate Addresses with Contacts

Above all, `addresses` and `contacts` can be connected with an optional One To Many relationship. Like so you could assign multiple contacts to an address and retrieve them like so:

```php
$address = config('lecturize.addresses.model', \Lecturize\Addresses\Models\Address::class)::find(1);
$contacts = $address->contacts;

foreach ($contacts as $contact) {
    //
}
```

```php
$contact = config('lecturize.contacts.model', \Lecturize\Addresses\Models\Contact::class)::find(1)
                  ->contacts()
                  ->first();
```

```php
$contact = config('lecturize.contacts.model', \Lecturize\Addresses\Models\Contact::class)::find(1);

return $contact->address->getHtml();
```

##### Geocoding

The address model provides a method `geocode()` which will try to fetch longitude and latitude through the Google Maps API. Please make sure to add your key within the services config file at `services.google.maps.key`. If you set the option `lecturize.addresses.geocode` to `true`, the package will automatically fire the `geocode()` method whenever an addresses model is saved (precisely we hook into the `saving` event).

## Changelog

- [2021-02-02] **v1.0** The `geocode` configuration option now defaults to `false`.
- [2022-05-16] **v1.1** Updated dependencies to PHP 8 and Laravel 8/9 - for older versions please refer to v1.0.
- [2023-02-21] **v1.2** Laravel 10 support.
- [2023-09-21] **v1.3** Support custom models for addresses and contacts, thanks to @bfiessinger. The geocoding feature now requires a Google Maps key, see 'Geocoding' above. Also, @bfiessinger has added fallback support for flags, see pull request #40 for further info.

## License

Licensed under [MIT license](http://opensource.org/licenses/MIT).

## Author

**Handcrafted with love by [Alexander Manfred Poellmann](https://twitter.com/AMPoellmann) in Vienna &amp; Rome.**
