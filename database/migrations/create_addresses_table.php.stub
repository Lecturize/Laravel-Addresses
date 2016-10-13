<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAddressesTable
 */
class CreateAddressesTable extends Migration
{
    /**
     * Table names
     */
    private $table_addresses;
    private $table_contacts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->table_addresses = config('lecturize.addresses.table', 'addresses');
        $this->table_contacts  = config('lecturize.contacts.table', 'contacts');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_addresses, function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('street',    60);
			$table->string('city',      60);
			$table->string('state',     60);
			$table->string('post_code', 10);

			$table->integer('country_id')->unsigned()->index();

            $table->string('note');

            $table->float('lat', 10, 6)->nullable();
            $table->float('lng', 10, 6)->nullable();

            $table->morphs('addressable');

			foreach( config('lecturize.addresses.flags', ['public', 'primary', 'billing', 'shipping']) as $flag ) {
				$table->boolean('is_'. $flag)->default(false)->index();
			}

			$table->timestamps();
			$table->softDeletes();
		});
        /*
        Schema::create($this->table_contacts, function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('type', 20);

            $table->string('first_name',  20)->nullable();
            $table->string('middle_name', 20)->nullable();
            $table->string('last_name',   20)->nullable();

            $table->string('company',  20)->nullable();
            $table->string('position', 20)->nullable();

            $table->string('phone',    16);
            $table->string('mobile',   16);
            $table->string('fax',      16);
            $table->string('email',    60);
            $table->string('website', 100);

            $table->morphs('contactable');

            foreach( config('lecturize.contacts.flags', ['public', 'primary']) as $flag ) {
                $table->boolean('is_'. $flag)->default(false)->index();
            }

            $table->longText('notes');

			$table->timestamps();
			$table->softDeletes();
		});
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    //  Schema::drop($this->table_contacts);
        Schema::drop($this->table_addresses);
    }
}