<?php namespace Lecturize\Addresses;

use Illuminate\Support\ServiceProvider;

/**
 * Class AddressesServiceProvider
 * @package Lecturize\Addresses
 */
class AddressesServiceProvider extends ServiceProvider
{
    protected $migrations = [
        'CreateAddressesTable' => 'create_addresses_table',
        'CreateContactsTable'  => 'create_contacts_table',

        'AddStreetExtraToAddressesTable' => 'add_street_extra_to_addresses_table',
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->handleMigrations();
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php', 'lecturize'
        );
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [];
    }

    /**
     * Publish migrations.
     *
     * @return void
     */
    private function handleMigrations()
    {
        foreach ($this->migrations as $class => $file) {
            if (! class_exists($class)) {
                $timestamp = date('Y_m_d_His', time());

                $this->publishes([
                    __DIR__ .'/../database/migrations/'. $file .'.php.stub' =>
                        database_path('migrations/'. $timestamp .'_'. $file .'.php')
                ], 'migrations');
            }
        }
    }
}