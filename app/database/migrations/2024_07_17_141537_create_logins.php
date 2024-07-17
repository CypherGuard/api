<?php

use Leaf\Schema;
use Leaf\Database;
use Illuminate\Database\Schema\Blueprint;

class CreateLogins extends Database
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        if (!static::$capsule::schema()->hasTable('logins')) :
            static::$capsule::schema()->create('logins', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('vault_id');
                $table->string('name');
                $table->string('username');
                $table->string('password');
                $table->string('url');
                $table->string('notes');
                $table->string('totp');
                $table->timestamps();
                $table->foreign('vault_id')->references('id')->on('vaults');
            });
        endif;

        // you can now build your migrations with schemas.
        // see: https://leafphp.dev/docs/mvc/schema.html
        // Schema::build('logins');
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        static::$capsule::schema()->dropIfExists('logins');
    }
}
