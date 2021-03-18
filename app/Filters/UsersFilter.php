<?php

namespace App\Filters;

class UsersFilter extends Filter
{
    public function name($value){
        $this->builder->where('name', 'like', "%$value%");
    }

    public function department($value){
        $this->builder->whereHas('department', function($query) use ($value){
            $query->where('title', 'like', "%$value%");
        });
    }

    public function phone($value){
        $this->builder->where('phone', 'like', "%$value%");
    }
}
