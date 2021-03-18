<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail($id)
 * @method static where(string $string, $id)
 */
class Record extends Model
{
    protected $fillable = [
        'date', 'hours', 'user_id', 'project_id', 'description', 'boss_id',
    ];

    protected $casts = [
        'date' => 'datetime:d.m.Y',
        'created_at' => 'datetime:d.m.Y H:i',
        'updated_at' => 'datetime:d.m.Y H:i'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function project(){
        return $this->belongsTo('App\Project');
    }

    public function department(){
        return $this->belongsTo('App\Department');
    }
}
