[![Latest Stable Version](https://poser.pugx.org/lecturize/laravel-addresses/v/stable)](https://packagist.org/packages/lecturize/laravel-addresses)
[![Total Downloads](https://poser.pugx.org/lecturize/laravel-addresses/downloads)](https://packagist.org/packages/lecturize/laravel-addresses)
[![License](https://poser.pugx.org/lecturize/laravel-addresses/license)](https://packagist.org/packages/lecturize/laravel-addresses)

# Laravel Addresses

Simple address and contact management for Laravel 5 with automatical geocoding to add longitude and latitude. Uses the famous [Countries](https://github.com/webpatser/laravel-countries) package by Webpatser.

## Important Notice

**This package is a work in progress**, please use with care and feel free to report any issues or ideas you may have!

We've transferred this package to a new owner and therefor updated the namespaces to **Lecturize\Addresses**. The config file is now `config/lecturize.php`.

## Installation

Require the package from your `composer.json` file

```php
"require": {
	"lecturize/laravel-addresses": "dev-master"
}
```

and run `$ composer update` or both in one with `$ composer require lecturize/laravel-addresses`.

Next register the following service providers and facades to your `config/app.php` file

```php
'providers' => [
    // Illuminate Providers ...
    // App Providers ...
    Lecturize\Addresses\AddressesServiceProvider::class,
    Webpatser\Countries\CountriesServiceProvider::class,
];
```

```php
'aliases' => [
	// Illuminate Facades ...
    'Countries' => Webpatser\Countries\CountriesFacade::class,
];
```

## Configuration & Migration

```bash
$ php artisan vendor:publish --provider="Webpatser\Countries\CountriesServiceProvider"
$ php artisan vendor:publish --provider="Lecturize\Addresses\AddressesServiceProvider"
```

This will create a `config/countries.php`, a `config/lecturize.php` and the migration files, that you'll have to run like so:

```bash
$ php artisan countries:migration
$ php artisan migrate
```

Check out [Webpatser\Countries](https://github.com/webpatser/laravel-countries) readme to see how to seed their countries data to your database.

## Usage

First, add our `HasAddresses` trait to your model.
        
```php
<?php namespace App\Models;

use Lecturize\Addresses\Traits\HasAddresses;

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

Available attributes are `street`, `city`, `post_code`, `state`, `country`, `state`, `note` (for internal use), `is_primary`, `is_billing` & `is_shipping`. Optionally you could also pass `lng` and `lat`, in case you deactivated the included geocoding functionality and want to add them yourself.

##### Check if Model has an Address
```php
if ($post->hasAddress()) {
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

**Handcrafted with love by [Alexander Manfred Poellmann](https://twitter.com/AMPoellmann) in Vienna &amp; Rome.**