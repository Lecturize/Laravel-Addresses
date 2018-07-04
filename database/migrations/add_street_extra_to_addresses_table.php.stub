<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddStreetExtraToAddressesTable
 */
class AddStreetExtraToAddressesTable extends Migration
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
        Schema::table($this->table, function(Blueprint $table){
            $table->string('street_extra', 60)->nullable()->after('street');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function(Blueprint $table){
            $table->dropColumn('street_extra');
        });
    }
}