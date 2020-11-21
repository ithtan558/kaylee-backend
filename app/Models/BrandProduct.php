<?php

namespace App\Models;

class BrandProduct extends BaseModel
{

    protected $table = 'brand_product';
    protected $fillable
        = [
            'brand_id',
            'product_id',
            'is_active',
            'is_delete'
        ];

}
