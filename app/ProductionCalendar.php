<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionCalendar extends Model
{
    protected $table = 'production_calendar';

    protected $fillable = [
        'month', 'year', 'hours'
    ];
}
