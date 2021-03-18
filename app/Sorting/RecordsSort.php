<?php

namespace App\Sorting;

class RecordsSort extends Sort
{
    public function name($value){
        $this->builder->select('records.*')->join('users', 'users.id', '=', 'records.user_id')->orderBy('name', $value);
    }

    public function project($value){
        $this->builder->select('records.*')->join('projects', 'projects.id', '=', 'records.project_id')->orderBy('title', $value);
    }

    public function date($value){
        $this->builder->orderBy('date', $value);
    }

    public function hours($value){
        $this->builder->orderBy('hours', $value);
    }

    public function created_at($value){
        $this->builder->orderBy('records.created_at', $value);
    }

    public function updated_at($value){
        $this->builder->orderBy('records.updated_at', $value);
    }
}
