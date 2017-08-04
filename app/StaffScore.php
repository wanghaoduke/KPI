<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffScore extends Model
{
    //
    protected $fillable = [
        'staff_user_id','rater_user_id','assessment_id','percentage','is_completed','ability','responsibility','prototype','finished_product','development_quality','develop_efficiency',
        "quality_score", "attitude_score"
    ];

    public function user(){
        return $this->belongsTo('App\User', 'staff_user_id', 'id');
    }
}
