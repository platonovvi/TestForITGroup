<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenresFilmsTable extends Migration
{
    public function up()
    {
        Schema::create('pivot_genres_films', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_genre');
            $table->foreign('id_genre')->references('id')->on('main_genres');

            $table->integer('id_film');
            $table->foreign('id_film')->references('id')->on('main_films');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pivot_genres_films');
    }
}