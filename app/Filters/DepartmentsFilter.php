<?php

namespace App\Filters;

class DepartmentsFilter extends Filter
{
    public function title($value){
        $this->builder->where('title', 'like', "%$value%");
    }
}
