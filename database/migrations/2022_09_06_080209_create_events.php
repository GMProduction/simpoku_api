<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('specialist_id')->unsigned();
            $table->string('title');
            $table->text('description');
            $table->text('image')->nullable();
            $table->date('start_at');
            $table->date('finish_at');
            $table->string('location');
            $table->double('latitude');
            $table->double('longitude');
            $table->text('announcement')->nullable();
            $table->timestamps();
            $table->foreign('specialist_id')->references('id')->on('specialists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
