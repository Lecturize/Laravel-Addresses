<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateContactsTable
 */
class CreateContactsTable extends Migration
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
        $this->table = config('lecturize.contacts.table', 'contacts');
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

            $table->string('type', 20)->default('default');

            $table->string('first_name',  20)->nullable();
            $table->string('middle_name', 20)->nullable();
            $table->string('last_name',   20)->nullable();

            $table->string('company',  60)->nullable();
            $table->string('position', 60)->nullable();

            $table->string('phone',    32)->nullable();
            $table->string('mobile',   32)->nullable();
            $table->string('fax',      32)->nullable();
            $table->string('email',    60)->nullable();
            $table->string('website', 100)->nullable();

            $table->integer('address_id')
                  ->nullable()
                  ->unsigned()
                  ->references('id')
                  ->on(config('lecturize.addresses.table', 'addresses'));

            $table->nullableMorphs('contactable');

            foreach(config('lecturize.contacts.flags', ['public', 'primary']) as $flag) {
                $table->boolean('is_'. $flag)->default(false)->index();
            }

            $table->longText('notes')->nullable();

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