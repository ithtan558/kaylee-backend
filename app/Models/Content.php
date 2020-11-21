<?php

namespace App\Models;

class Content extends BaseModel
{

    protected $table = 'content';
    protected $fillable
        = [
            'name',
            'slug',
            'code',
            'description',
            'content',
            'image',
            'created_by',
            'updated_by'
        ];

}
