<?php

namespace App\Models;

class Brand extends BaseModel
{

    protected $table = 'brand';
    protected $fillable
        = [
            'client_id',
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
            'created_by',
            'updated_by'
        ];

}
