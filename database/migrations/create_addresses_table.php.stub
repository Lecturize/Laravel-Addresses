<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAddressesTable
 */
class CreateAddressesTable extends Migration
{
    /**
     * Table names.
     *
     * @var string  $table  The main table name for this migration.
     */
    protected $table;

    /**
     * Create a new migration instance.
     */
    public function __construct()
    {
        $this->table = config('lecturize.addresses.table', 'addresses');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('street',    60)->nullable();
            $table->string('city',      60)->nullable();
            $table->string('state',     60)->nullable();
            $table->string('post_code', 10)->nullable();

            $table->integer('country_id')->nullable()->unsigned()->index();

            $table->string('note')->nullable();

            $table->float('lat', 10, 6)->nullable();
            $table->float('lng', 10, 6)->nullable();

            $table->nullableMorphs('addressable');

            foreach(config('lecturize.addresses.flags', ['public', 'primary', 'billing', 'shipping']) as $flag) {
                $table->boolean('is_'. $flag)->default(false)->index();
            }

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}