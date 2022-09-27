<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRegistrantMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_registrant_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_registrant__id');
            $table->string('code');
            $table->string('name');
            $table->string('phone');
            $table->timestamps();
            $table->foreign('event_registrant__id')->references('id')->on('event_registrants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_registrant_members');
    }
}
