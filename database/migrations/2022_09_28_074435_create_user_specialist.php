<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSpecialist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_specialist', function (Blueprint $table) {
            $table->primary(['user_id', 'specialist_id']);
            $table->uuid('user_id');
            $table->bigInteger('specialist_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('specialist_id')
                ->references('id')
                ->on('specialists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_specialist');
    }
}
