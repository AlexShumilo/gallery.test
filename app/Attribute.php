<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [                                 // установка полей таблицы, которые можно заполнять
      'height', 'width', 'size', 'views', 'image_id'
    ];

    public function image() {                               // установка связи
        return $this->belongsTo('App\Image');
    }
}
