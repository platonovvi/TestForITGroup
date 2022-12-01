<?php

namespace App\Http\Controllers;

use App\Models\Main\Films;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FilmsController extends Controller
{

    /* Вывод списка фильмов */
    public function search(int $page = 1, int $id_film = null)
    {
        try {
            $request = request();

            // Принимаем данные двумя способами:
            // через url, если нет - принимаем с фронта через input
            $page = $page | request()->input('page'); // Номер страницы
            $id_film = $id_film | $request->input('id_film');// ID фильма

            $page_size = 5; // Задаем кол-во записей на страницу
            $first_item = ($page - 1) * $page_size; // Вычисляем индекс первого фильма для страницы
            $last_item = $first_item + $page_size; // Вычисляем индекс последнего фильма для страницы

            $films = Films::query();
            //Подключение жанров через зависимости в Models
            $films->with('pivot_genres.genres');

            if ($id_film) { // Фильтр по ID фильма
                $films->where('main_films.id', $id_film);
            }

            //Подключение жанров через join, списком в "genres"
            $films->join('pivot_genres_films', 'pivot_genres_films.id_film', 'main_films.id')
                ->join('main_genres', 'pivot_genres_films.id_genre', 'main_genres.id')
                ->selectRaw('main_films.*')->selectRaw('array_agg(main_genres.name) as genres')
                ->groupBy('main_films.id');

            $films = $films->get();
            $all = $films->count(); // Общее кол-во фильмов в выборке
            $last_page = ceil(($all ? $all : 1) / $page_size); // Вычисляем номер последней страницы

            // Выбираем записи по странице
            $films->skip($first_item);
            $films->take($page_size);

            return self::send_success([
                'films' => $films,

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

    /* Вывод фильма по ID */
    public function search_film(int $id_film = null)
    {
        try {
            $request = request();
            // Принимаем данные двумя способами:
            // через url, если нет - принимаем с фронта через input
            $id_film = $id_film | $request->input('id_film');// ID фильма

            $films = Films::query();

            if ($id_film) { // Фильтр по ID фильма
                $films->where('main_films.id', $id_film);
            }
            //Подключение жанров через зависимости в Models
            $films->with('pivot_genres.genres');
            //Подключение жанров через join, списком в "genres"
            $films->join('pivot_genres_films', 'pivot_genres_films.id_film', 'main_films.id')
                ->join('main_genres', 'pivot_genres_films.id_genre', 'main_genres.id')
                ->selectRaw('main_films.*')->selectRaw('array_agg(main_genres.name) as genres')
                ->groupBy('main_films.id');
            $films = $films->get();
            return self::send_success([
                'films' => $films,
            ]);
        } catch (Exception $error) {
            return self::send_error($error->getMessage());
        }
    }

    /* Создание/редактирование фильма */
    public function save()
    {
        try {
            DB::begintransaction();
            $request = request();
            $id = $request->input('id');
            $poster_link = $request->input('poster_link');

            if ($poster_link) {
                $rules = [
                    'name' => 'required|string',
                    'poster_link' => 'string|unique:main_films,poster_link', // Проверка на то, что ссылка является строкой, а название файла уникально
                    'file' => 'mimes:jpeg,jpg,bmp,png',
                ];
            } else {
                $rules = [
                    'name' => 'required|string',
                ];
            }

            $data = [
                'name' => $request->input('name'),
                //Статус при создании по-умолчанию будет "Не опубликован",а при редактировании не изменяться
                'poster_link' => $poster_link,
                'file' => $request->file('file')
            ];

            if ($error = self::validation($data, $rules)) {
                throw new Exception($error);
            }

            // При загрузке файла формируется ссылка на сервер: адркс сервера + путь в папку posters
            // + название картинки. Если файл не был загружен - присваивается ссылка на дефолтную картинку
            if ($poster_link && $data['file']) {
                $upload_folder = 'public/posters';
                $filename = $poster_link;
                Storage::putFileAs($upload_folder, $data['file'], $filename);
                $data['poster_link'] = env('APP_URL') . 'posters/' . $poster_link;
            } else {
                $data['poster_link'] = env('APP_URL') . 'posters/default.jpg';
            }

            if ($id) {
                $films = Films::where('id', $id)->get();
                if (!$films->count()) {
                    throw new Exception('Фильм не найден');
                }
                $film = $films->first();
                $film->update($data);
                $film->save();
            } else {
                Films::create($data);
            }
            DB::commit();
            return self::send_success();
        } catch (Exception $error) {
            DB::rollback();
            return self::send_error($error->getMessage());
        }
    }

    /* Удаление фильма */
    public function delete()
    {
        try {
            DB::begintransaction();
            $request = request();

            $film = Films::where('id', $request->input('id'))->get();

            if (!$film->count()) {
                throw new Exception('Фильм не найден');
            }

            $film = $film->first();
            $film->delete();
            DB::commit();
            return self::send_success();
        } catch (Exception $error) {
            DB::rollback();
            return self::send_error($error->getMessage());
        }
    }

    /* Изменение статуса */
    public function change_status()
    {
        try {
            DB::begintransaction();
            $request = request();
            $id = $request->input('id');
            $status = $request->input('status');

            $films = Films::where('id', $id)->get();
            if (!$films->count()) {
                throw new Exception('Фильм не найден');
            }
            $films = $films->first();
            $rules = [
                'status' => 'numeric',
            ];
            $data = [
                'status' => $status,
            ];
            if ($error = self::validation($data, $rules)) {
                throw new Exception($error);
            }
            $films->update($data);
            $films->save();

            DB::commit();
            return self::send_success();
        } catch (Exception $error) {
            DB::rollback();
            return self::send_error($error->getMessage());
        }
    }
}
