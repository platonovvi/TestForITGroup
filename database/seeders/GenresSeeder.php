<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenresSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        try {
            $genres = Excel::toArray(null, 'database/seeders/excel/genres.xlsx')[0];
            print_r('Загрузка данных по потокам - ' . "\n");
            foreach ($genres as $key => $genre) {
                print_r('Обработано - ' . $key . "\r");
                if ($key > 0) {
                    DB::table('main_genres')->insert([
                        'name' => trim($genre[0]),
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