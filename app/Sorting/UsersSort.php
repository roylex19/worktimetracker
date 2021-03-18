<?php

namespace App\Sorting;

class UsersSort extends Sort
{
    public function name($value){
        $this->builder->orderBy('name', $value);
    }

    public function department($value){
        $this->builder->select('users.*')->join('departments', 'departments.id', '=', 'users.department_id')->orderBy('title', $value);
    }

    public function phone($value){
        $this->builder->orderBy('phone', $value);
    }

    public function email($value){
        $this->builder->orderBy('email', $value);
    }

    public function created_at($value){
        $this->builder->orderBy('created_at', $value);
    }

    public function updated_at($value){
        $this->builder->orderBy('updated_at', $value);
    }
}
