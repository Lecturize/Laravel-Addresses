[![Latest Stable Version](https://poser.pugx.org/vendocrat/laravel-addresses/v/stable)](https://packagist.org/packages/vendocrat/laravel-addresses)
[![Total Downloads](https://poser.pugx.org/vendocrat/laravel-addresses/downloads)](https://packagist.org/packages/vendocrat/laravel-addresses)
[![License](https://poser.pugx.org/vendocrat/laravel-addresses/license)](https://packagist.org/packages/vendocrat/laravel-addresses)

# Laravel Addresses

Simple address and contact management with polymorph many-to-many relations.

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

This will create a `config/addresses.php` and a migration file. In the config file you can customize the table names, finally you'll have to run migration like so:

```bash
$ php artisan migrate
```

## License

Licensed under [MIT license](http://opensource.org/licenses/MIT).

## Author

**Handcrafted with love by [Alexander Manfred Poellmann](http://twitter.com/AMPoellmann) for [vendocrat](https://vendocr.at) in Vienna &amp; Rome.**