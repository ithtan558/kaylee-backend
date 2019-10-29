<?php

namespace App\Models;

class City extends BaseModel
{

    protected $table = 'city';
    protected $fillable
        = [
            'name',
            'created_by',
            'updated_by'
        ];

}
