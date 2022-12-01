<?php

namespace App\Models\Pivot;
use App\Models\Main\Genres;
use Illuminate\Database\Eloquent\Model;

class PivotGenresFilms extends Model
{
    protected $table = 'pivot_genres_films';
    protected $fillable = [
        'id',
        'id_genre',
        'id_film',
    ];

    public function genres()
    {
        return $this->belongsTo(Genres::class, 'id_genre', 'id');
    }
}
