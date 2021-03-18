<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
/**
 * @method static findOrFail($id)
 */
class Department_view extends Model
{
    protected $fillable = [
        'user_id', 'departments',
    ];

    protected $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
        'updated_at' => 'datetime:d.m.Y H:i',
        'departments' => 'json'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function department(){
        return $this->belongsTo('App\Department');
    }
}
