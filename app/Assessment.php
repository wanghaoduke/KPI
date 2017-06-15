<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    //
    protected $fillable = [
        'year', 'month', 'is_completed',
    ];
}
