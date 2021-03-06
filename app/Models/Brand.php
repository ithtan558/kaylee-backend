<?php

namespace App\Models;

class Brand extends BaseModel
{

    protected $table = 'brand';
    protected $fillable
        = [
            'client_id',
            'name',
            'phone',
            'location',
            'wards_id',
            'district_id',
            'city_id',
            'image',
            'start_time',
            'end_time',
            'is_active',
            'is_delete',
            'created_by',
            'updated_by'
        ];

}
