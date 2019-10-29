<?php

namespace App\Models;

class ServiceCategory extends BaseModel
{

    protected $table = 'service_category';
    protected $fillable
        = [
            'name',
            'description',
            'image',
            'created_by',
            'updated_by'
        ];

}
