<?php

namespace App\Models\Main;
use App\Models\Pivot\PivotGenresFilms;
use Illuminate\Database\Eloquent\Model;

class Films extends Model
{
    protected $table = 'main_films';
    protected $fillable = [
        'id',
        'name',
        'status',
        'poster_link',
    ];

    public function pivot_genres()
    {
        return $this->hasMany(PivotGenresFilms::class, 'id_film', 'id');
    }
}


