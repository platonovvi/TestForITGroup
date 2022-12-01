<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        try {
            $films = Excel::toArray(null, 'database/seeders/excel/films.xlsx')[0];
            print_r('Загрузка данных по потокам - ' . "\n");
            foreach ($films as $key => $film) {
                print_r('Обработано - ' . $key . "\r");
                if ($key > 0) {
                    DB::table('main_films')->insert([
                        'name' => trim($film[0]),
                        'status' => intVal(trim($film[1])),
                        'poster_link' => trim($film[2]),
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