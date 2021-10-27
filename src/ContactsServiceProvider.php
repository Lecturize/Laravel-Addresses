<?php

namespace Kwidoo\Contacts;

use Illuminate\Support\ServiceProvider;

/**
 * Class AddressesServiceProvider
 * @package Kwidoo\Contacts
 */
class ContactsServiceProvider extends ServiceProvider
{
    /** @var string[]|array */
    protected $migrations = [
        'CreateContactsTable'  => 'create_contacts_table',
    ];

    public function boot()
    {
        $this->handleConfig();
        $this->handleMigrations();

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'contacts');
    }

    /** @inheritdoc */
    public function register()
    {
        //
    }

    /** @inheritdoc */
    public function provides(): array
    {
        return [];
    }

    /**
     * Publish and merge the config file.
     *
     * @return void
     */
    private function handleConfig()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->publishes([$configPath => config_path('contacts.php')]);

        $this->mergeConfigFrom($configPath, 'contacts');
    }

    /**
     * Publish migrations.
     *
     * @return void
     */
    private function handleMigrations()
    {
        $count = 0;
        foreach ($this->migrations as $class => $file) {
            if (!class_exists($class)) {
                $timestamp = date('Y_m_d_Hi' . sprintf('%02d', $count), time());

                $this->publishes([
                    __DIR__ . '/../database/migrations/' . $file . '.php.stub' =>
                    database_path('migrations/' . $timestamp . '_' . $file . '.php')
                ], 'migrations');

                $count++;
            }
        }
    }
}
