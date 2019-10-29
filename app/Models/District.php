<?php

namespace App\Models;

class District extends BaseModel
{

    protected $table = 'district';
    protected $fillable
        = [
            'city_id',
            'name',
            'description',
            'address',
            'wards_id',
            'district_id',
            'city_id',
            'image',
            'created_by',
            'updated_by'
        ];

}
