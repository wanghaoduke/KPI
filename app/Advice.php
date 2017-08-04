<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advice extends Model
{
    //
    protected $fillable = [
        'title', 'content', 'suggest_user_id', 'rater_user_id', 'is_processed', 'is_accept', 'score'
    ];
}
