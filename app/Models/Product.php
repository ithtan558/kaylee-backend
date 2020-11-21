<?php

namespace App\Models;

class Product extends BaseModel
{

    protected $table = 'product';
    protected $fillable
        = [
            'client_id',
            'supplier_id',
            'category_id',
            'code',
            'name',
            'price',
            'description',
            'image',
            'created_by',
            'updated_by'
        ];

}
