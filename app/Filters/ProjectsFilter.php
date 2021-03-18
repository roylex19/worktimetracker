<?php

namespace App\Filters;

class ProjectsFilter extends Filter
{
    public function title($value){
        $this->builder->where('title', 'like', "%$value%");
    }
}
