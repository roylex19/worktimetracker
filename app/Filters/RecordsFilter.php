<?php

namespace App\Filters;

class RecordsFilter extends Filter
{
    public function name($value){
        $this->builder->whereHas('user', function($query) use ($value){
           $query->where('name', 'like', "%$value%");
        });
    }

    public function project($value){
        $this->builder->whereHas('project', function($query) use ($value){
            $query->where('title', 'like', "%$value%");
        });
    }

    public function hours($value){
        $this->builder->where('hours', $value);
    }

    public function date($value){
        if(!$value) return;

        $period = json_decode($value);
        if(is_object($period)){
            if($period->start && !$period->end){
                $this->builder->whereDate('date', '>=', $period->start);
                return;
            }else if($period->end && !$period->start){
                $this->builder->whereDate('date', '<=', $period->end);
                return;
            }

            $this->builder->whereBetween('date', [$period->start, $period->end]);
            return;
        }

        $this->builder->whereDate('date', $value);
    }

    public function description($value){
        $this->builder->where('description', 'like', "%$value%");
    }

    public function created_at($value){
        $this->builder->whereDate('records.created_at', $value);
    }

    public function updated_at($value){
        $this->builder->whereDate('records.updated_at', $value);
    }
}
