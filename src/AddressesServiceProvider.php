<?php namespace vendocrat\Addresses;

use Illuminate\Support\ServiceProvider;

class AddressesServiceProvider extends ServiceProvider
{
	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ .'/../config/config.php' => config_path('addresses.php')
		], 'config');

		if ( ! class_exists('CreateAddressesTable') ) {
			$timestamp = date('Y_m_d_His', time());

			$this->publishes([
				__DIR__ .'/../database/migrations/create_addresses_table.php.stub' =>
					database_path('migrations/'. $timestamp .'_create_addresses_table.php')
			], 'migrations');
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ .'/../config/config.php',
			'addresses'
		);

		$this->app->singleton(Addresses::class);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return string[]
	 */
	public function provides()
	{
		return [
			Addresses::class
		];
	}
}