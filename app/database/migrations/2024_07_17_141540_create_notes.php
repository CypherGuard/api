<?php

use Leaf\Schema;
use Leaf\Database;
use Illuminate\Database\Schema\Blueprint;

class CreateNotes extends Database
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        if (!static::$capsule::schema()->hasTable('notes')) :
            static::$capsule::schema()->create('notes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('vault_id');
                $table->string('name');
                $table->string('data');
                $table->string('notes');
                $table->timestamps();
                $table->foreign('vault_id')->references('id')->on('vaults');
            });
        endif;

        // you can now build your migrations with schemas.
        // see: https://leafphp.dev/docs/mvc/schema.html
        // Schema::build('notes');
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        static::$capsule::schema()->dropIfExists('notes');
    }
}
