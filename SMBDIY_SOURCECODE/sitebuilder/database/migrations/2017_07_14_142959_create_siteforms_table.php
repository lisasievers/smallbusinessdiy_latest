<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siteforms', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('user_id');
             $table->integer('site_category');
            $table->integer('site_id');
            $table->string('site_name');
            $table->string('payid');
            
            $table->string('home_title');
            $table->string('home_text');
            $table->string('products_title');
            $table->string('contact_address');
            $table->string('google_map');
            $table->string('sliderFile');
            $table->string('userFile');
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
        Schema::drop('siteforms');
    }
}
