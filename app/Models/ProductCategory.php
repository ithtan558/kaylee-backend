<?php

namespace App\Models;

class ProductCategory extends BaseModel
{

    protected $table = 'product_category';
    protected $fillable
        = [
            'client_id',
            'supplier_id',
            'code',
            'name',
            'description',
            'sequence',
            'image',
            'is_active',
            'is_delete',
            'created_by',
            'updated_by'
        ];

}
