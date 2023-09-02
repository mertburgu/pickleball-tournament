<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->tinyInteger('game_format')->default(0)->comment('0: 1vs1, 1: 2vs2');
            $table->tinyInteger('score_format')->default(0)->comment('0: 11, 1: 15, 2:21');
            $table->tinyInteger('tournament_format')->default(0)->comment('0: round_robin');
            $table->integer('player_limit')->unsigned()->nullable(false);
            $table->integer('average_game_time')->unsigned()->default(0)->comment('0: 15, 1: 20, 2:25');
            $table->integer('number_of_courts')->unsigned()->nullable(false);
            $table->boolean('started')->default(false);
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
        Schema::dropIfExists('tournaments');
    }
};
