<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('street_id');
            $table->string('address', 64);
            $table->string('type', 16)->nullable();
            $table->string('status', 16)->nullable();
            $table->text('note')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        Schema::create('streets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('city_id');
            $table->string('name', 32);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index('city_id');
            $table->index('name');
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 24);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suites');
        Schema::dropIfExists('streets');
        Schema::dropIfExists('cities');
    }
}
