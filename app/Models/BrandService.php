<?php

namespace App\Models;

class BrandService extends BaseModel
{

    protected $table = 'brand_service';
    protected $fillable
        = [
            'brand_id',
            'service_id',
            'is_active',
            'is_delete'
        ];

}
