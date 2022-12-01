<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PivotGenresFilmsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        try {
            $pivot = Excel::toArray(null, 'database/seeders/excel/pivot_genres_films.xlsx')[0];
            print_r('Загрузка данных по потокам - ' . "\n");
            foreach ($pivot as $key => $item) {
                print_r('Обработано - ' . $key . "\r");
                if ($key > 0) {
                    DB::table('pivot_genres_films')->insert([
                        'id_genre' => trim($item[0]),
                        'id_film' => trim($item[1]),
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $error) {
            DB::rollback();
            dd($error);
        }
    }
}