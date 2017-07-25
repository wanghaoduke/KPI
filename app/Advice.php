<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advice extends Model
{
    //
    protected $fillable = [
        'title', 'content', 'suggest_id', 'rater_id', 'is_processed', 'is_accept', 'score'
    ];
}
