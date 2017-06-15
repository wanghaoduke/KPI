<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffScore extends Model
{
    //
    protected $fillable = [
        'staff_id','rater_id','assessment_id','percentage','is_completed','ability','responsibility','prototype','finished_product','development_quality','develop_efficiency'
    ];

    public function user(){
        return $this->belongsTo('App\User', 'staff_id', 'id');
    }
}
