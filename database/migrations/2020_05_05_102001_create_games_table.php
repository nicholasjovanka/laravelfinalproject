<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('gameName');
            $table->string("gameImage")->nullable($value=true);
            $table->string('gameDescription',1000);
            $table->string('gameTrailer')->nullable($value=true);
            $table->json('platform')->nullable($value=true);
            $table->boolean('onSteam')->nullable($value=true);
            $table->integer('AgeRating')->unsigned()->nullable($value=true);
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
        Schema::dropIfExists('games');
    }
}
