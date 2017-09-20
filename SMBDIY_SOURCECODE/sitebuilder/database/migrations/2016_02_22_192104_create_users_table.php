<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip_address', 32);
            $table->string('email');
            $table->string('password');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('type', 20);
            $table->string('city', 50);
            $table->string('state', 50);
            $table->string('activation_code', 40)->nullable();
            $table->string('forgotten_password_code', 40)->nullable();
            $table->tinyInteger('active');
            $table->string('provider');
            $table->string('provider_id');
            $table->rememberToken();
            $table->string('user_from', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
