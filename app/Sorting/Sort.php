<?php

namespace App\Sorting;

class Sort{
    protected $builder;
    protected $request;

    public function __construct($builder, $request){
        $this->builder = $builder;
        $this->request = $request;
    }

    public function apply(){
        if(empty($this->sorts()))
            return $this->builder->latest();

        foreach ($this->sorts() as $sort => $value){
            if(method_exists($this, $sort) && $value){
                $this->$sort($value);
            }
        }
        return $this->builder;
    }

    public function sorts(){
        $sorts = [];

        if($this->request->sort) {
            $sorts = json_decode($this->request->sort, true);

            $sorts = array_filter($sorts, function($el){
                return $el !== null;
            });
        }

        return $sorts;
    }
}
