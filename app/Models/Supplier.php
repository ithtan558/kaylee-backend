<?php

namespace App\Models;

class Supplier extends BaseModel
{

    protected $table = 'supplier';
    protected $fillable
        = [
            'code',
            'name',
            'description',
            'image',
            'facebook',
            'created_by',
            'updated_by'
        ];

}
