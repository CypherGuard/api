<?php

use Leaf\Schema;
use Leaf\Database;
use Illuminate\Database\Schema\Blueprint;

class CreateVaults extends Database
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        if (!static::$capsule::schema()->hasTable('vaults')) :
            static::$capsule::schema()->create('vaults', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->integer('owner_id');
                $table->foreign('owner_id')->references('id')->on('users');
                $table->string('shared_id')->nullable();
                $table->timestamps();
            });
        endif;

        // you can now build your migrations with schemas.
        // see: https://leafphp.dev/docs/mvc/schema.html
        // Schema::build('vaults');
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        static::$capsule::schema()->dropIfExists('vaults');
    }
}
