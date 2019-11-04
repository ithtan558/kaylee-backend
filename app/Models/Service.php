<?php

namespace App\Models;

class Service extends BaseModel
{

    protected $table = 'service';
    protected $fillable
        = [
            'client_id',
            'name',
            'code',
            'description',
            'category_id',
            'time',
            'price',
            'image',
            'created_by',
            'updated_by'
        ];

}
