<?php

namespace App\Http\Controllers;

use App\Models\Main\Genres;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GenresController extends Controller
{
    /* Вывод списка жанров */
    public function search()
    {
        try {
            $request = request();

            $genres = Genres::query();
            $genres = $genres->get();
            return self::send_success([
                'genres' => $genres,
            ]);
        } catch (Exception $error) {
            return self::send_error($error->getMessage());
        }
    }

    /* Все фильмы в данном жанре с разбивкой на страницы */
    public function search_films(int $page = 1, int $id_genre = null)
    {
        try {
            $request = request();
            $page = $page | request()->input('page'); // Номер страницы
            $id_genre = $id_genre | $request->input('genre');// ID жанра

            $page_size = 5; // Задаем кол-во записей на страницу
            $first_item = ($page - 1) * $page_size; // Вычисляем индекс первого фильма для страницы
            $last_item = $first_item + $page_size; // Вычисляем индекс последнего фильма для страницы

            // Достаем весь список жанров
            $films = Genres::join('pivot_genres_films', 'pivot_genres_films.id_genre', 'main_genres.id');
            if ($id_genre) { // Фильтр по ID жанра
                $films->where('pivot_genres_films.id_genre', $id_genre);
            }
            $films->join('main_films', 'main_films.id', 'pivot_genres_films.id_film')
                ->selectRaw('main_films.*, array_agg(main_genres.name) as genres')
                ->groupBy('main_films.id');
            $films = $films->get();
            $all = $films->count(); // Общее кол-во фильмов в выборке
            $last_page = ceil(($all ? $all : 1) / $page_size); // Вычисляем номер последней страницы

            // Выбираем записи по странице
            $films->skip($first_item);
            $films->take($page_size);

            return self::send_success([
                '$films' => $films,

                // Вывод для пагинации на фронте
                'page' => $page,
                'count_all' => $all,
                'first_item' => $first_item + 1,
                'last_item' => $last_item,
                'last_page' => $last_page
            ]);
        } catch (Exception $error) {
            return self::send_error($error->getMessage());
        }
    }
}
