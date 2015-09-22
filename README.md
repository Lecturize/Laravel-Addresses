[![Latest Stable Version](https://poser.pugx.org/vendocrat/laravel-addresses/v/stable)](https://packagist.org/packages/vendocrat/laravel-addresses)
[![Total Downloads](https://poser.pugx.org/vendocrat/laravel-addresses/downloads)](https://packagist.org/packages/vendocrat/laravel-addresses)
[![License](https://poser.pugx.org/vendocrat/laravel-addresses/license)](https://packagist.org/packages/vendocrat/laravel-addresses)

# Laravel Addresses

Simple address and contact management for Laravel 5 with automatical geocoding to add longitude and latitude. Uses the famous [Countries](https://github.com/webpatser/laravel-countries) package by Webpatser.

## Installation

Require the package from your `composer.json` file

```php
"require": {
	"vendocrat/laravel-addresses": "dev-master"
}
```

and run `$ composer update` or both in one with `$ composer require vendocrat/laravel-addresses`.

Next register the following service providers and facades to your `config/app.php` file

```php
'providers' => [
    // Illuminate Providers ...
    // App Providers ...
    vendocrat\Addresses\AddressesServiceProvider::class,
    Webpatser\Countries\CountriesServiceProvider::class,
];
```

```php
'providers' => [
	// Illuminate Facades ...
    'Address'   => vendocrat\Addresses\Facades\Addresses::class,
    'Countries' => Webpatser\Countries\CountriesFacade::class,
];
```

## Configuration & Migration

```bash
$ php artisan vendor:publish
$ php artisan countries:migration
```

This will create a `config/addresses.php` and the migration files. In the config file you can customize the table names, finally you'll have to run migration like so:

```bash
$ php artisan migrate
```

Check out [Webpatser\Countries](https://github.com/webpatser/laravel-countries) readme to see how to seed their countries data to your database.

## Usage

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

Available attributes are `street`, `city`, `post_code`, `state`, `country`, `state`, `note` (for internal use), `is_primary`, `is_billing` & `is_shipping`. Optionally you could also pass `lng` and `lat`, in case you deactivated the included geocoding functionality and want to add them yourself.

##### Check if Model has an Address
```php
if ( $post->hasAddress() ) {
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

## License

Licensed under [MIT license](http://opensource.org/licenses/MIT).

## Author

**Handcrafted with love by [Alexander Manfred Poellmann](http://twitter.com/AMPoellmann) for [vendocrat](https://vendocr.at) in Vienna &amp; Rome.**