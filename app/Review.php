<?php

namespace App;

use Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use Traits\HasCompositePrimaryKey;
    protected $fillable = [
        'userReview', 'user_id', 'game_id', 'userScore '
    ];
    protected $primaryKey =['user_id','game_id'];
    public $incrementing = false;

    public function user(){
        return $this->hasOne('App\User');
    }

    public function game(){
        return $this->hasOne('App\Game');
    }

}
