<?php

namespace App\Models\Main;
use Illuminate\Database\Eloquent\Model;

class Genres extends Model
{
    protected $table = 'main_genres';
    protected $fillable = [
        'id',
        'name',
    ];
}
