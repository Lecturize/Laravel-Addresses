<?php

namespace Lecturize\Addresses;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;

/**
 * Class AddressesServiceProvider
 * @package Lecturize\Addresses
 */
class AddressesServiceProvider extends ServiceProvider
{
    protected array $migrations = [
        'CreateAddressesTable' => 'create_addresses_table',
        'CreateContactsTable'  => 'create_contacts_table',

        'AddStreetExtraToAddressesTable' => 'add_street_extra_to_addresses_table',
        'AddGenderToContactsTable'       => 'add_gender_to_contacts_table',
        'AddTitleToContactsTable'        => 'add_title_to_contacts_table',

        'AddPropertiesToAddressesTable' => 'add_properties_to_addresses_table',
        'AddPropertiesToContactsTable'  => 'add_properties_to_contacts_table',

        'AddUserIdToAddressesTable'          => 'add_user_id_to_addresses_table',
        'AddUuidToAddressesAndContactsTable' => 'add_uuid_to_addresses_and_contacts_table',
        'AddVatIdToContactsTable'            => 'add_vat_id_to_contacts_table',
        'AddEmailInvoiceToContactsTable'     => 'add_email_invoice_to_contacts_table',
    ];

    public function boot()
    {
        $this->handleMigrations();

        $this->loadTranslationsFrom(__DIR__ .'/../resources/lang', 'addresses');
    }

    /** @inheritdoc */
    public function register()
    {
        $this->handleConfig();
    }

    /** @inheritdoc */
    public function provides(): array
    {
        return [];
    }

    /**
     * Merge the given configuration with the existing configuration.
     * This implementation of mergeConfigFrom respects multi dimensional configs.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeConfig(array $original, array $merging)
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }

    private function handleConfig(): void
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->publishes([$configPath => config_path('lecturize.php')]);

        $this->mergeConfigFrom($configPath, 'lecturize');
    }

    private function handleMigrations(): void
    {
        $count = 0;
        foreach ($this->migrations as $class => $file) {
            if (! class_exists($class)) {
                $timestamp = date('Y_m_d_Hi'. sprintf('%02d', $count), time());

                $this->publishes([
                    __DIR__ .'/../database/migrations/'. $file .'.php.stub' =>
                        database_path('migrations/'. $timestamp .'_'. $file .'.php')
                ], 'migrations');

                $count++;
            }
        }
    }
}
