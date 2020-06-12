<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    //
    protected $fillable = [
        'gameName', 'gameImage', 'gameDescription', 'gameTrailer','gamePublisher','gameReleaseDate','platform','onSteam','AgeRating', 'steamId',
    ];

    protected $casts = [
        'platform' => 'array'
    ];

    public function review(){
        return $this->hasMany('App\Review');
    }

}
