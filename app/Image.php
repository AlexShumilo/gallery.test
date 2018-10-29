<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['name'];                             // установка полей таблицы, которые можно заполнять

    public function attribute() {                               // установка отношения Один-к-Одному
        return $this->hasOne('App\Attribute');
    }
}
