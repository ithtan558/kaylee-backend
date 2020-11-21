<?php

namespace App\Models;

class CategoryContent extends BaseModel
{

    protected $table = 'category_content';
    protected $fillable
        = [
            'name',
            'code',
            'description',
            'created_by',
            'updated_by'
        ];

}
