<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'department', 'Jurisdiction', 'status', 'is_default_development', 'is_default_plan', 'percentage_plan', 'percentage_development', 'is_senior_manager'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function staffScores (){
        return $this->hasMany('App\StaffScore', 'staff_id', 'id');
    }
}
