<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentStaffFinalScore extends Model
{
    //
    protected $fillable = [
        'user_id', 'assessment_id', 'sum_score', 'quality_score', 'attitude_score', 'advices_score', 'assessment_date'
    ];
    protected $table = 'assessment_staff_final_scores';
}
