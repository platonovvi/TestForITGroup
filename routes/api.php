<?php

use Illuminate\Http\Request;
use \App\Http\Controllers\FilmsController;
use \App\Http\Controllers\GenresController;

    Route::prefix('films')->group(function () {
        // Сохранение/редактирование фильмов
        Route::post('/save', 'FilmsController@save');
        // Изменение статуса фильма (публикация)
        Route::post('/change_status', 'FilmsController@change_status');
        // Удаление фильма
        Route::post('/delete', 'FilmsController@delete');
        // Поиск фильма по ID
        Route::post('/search_film/{id_film?}', [FilmsController::class, 'search_film']);
        // Список всех фильмов с разбивкой на страницы
        Route::post('/search/{page?}', [FilmsController::class, 'search']);
    });

    Route::prefix('genres')->group(function () {
        // Список всех жанров
        Route::post('/search', [GenresController::class, 'search']);
        // Все фильмы в данном жанре с разбивкой на страницы
        Route::post('/search_films/{page?}/{id_genre?}', [GenresController::class, 'search_films']);
    });


