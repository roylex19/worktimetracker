<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @method static find($id)
 * @method static create(array $array)
 * @method static whereExists(\Closure $param)
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'department_id', 'phone', 'token', 'password', 'disabled'
    ];

    protected $hidden = [
        'password', 'token'
    ];

    protected $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
        'updated_at' => 'datetime:d.m.Y H:i'
    ];

    public function records(){
        return $this->hasMany('App\Record', 'user_id');
    }

    public function department(){
        return $this->belongsTo('App\Department')->withDefault([
            'title' => '',
        ]);
    }

    public function department_view(){
        return $this->hasMany('App\Department_view', 'user_id');
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
