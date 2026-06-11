<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'firstname','lastname','login','group','description', 'email', 'password',
    ];

    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($pass){

        $this->attributes['password'] = bcrypt($pass);

    }

    public function subjects()
    {
        return $this->belongsToMany('App\Subject', 'teacher_subject', 'user_id', 'subject_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'user_role', 'user_id', 'role_id');
    }

    public function hasRole($name)
    {
        foreach ($this->roles as $role) {
            if ($role->name === $name) return true;
        }
        return $this->group === $name;
    }

}
