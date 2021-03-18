<?php

namespace App\Sorting;

class DepartmentsSort extends Sort
{
    public function title($value){
        $this->builder->orderBy('title', $value);
    }

    public function created_at($value){
        $this->builder->orderBy('created_at', $value);
    }

    public function updated_at($value){
        $this->builder->orderBy('updated_at', $value);
    }
}
