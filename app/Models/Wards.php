<?php

namespace App\Models;

class Wards extends BaseModel
{

    protected $table = 'wards';
    protected $fillable
        = [
            'city_id',
            'district_id',
            'name',
            'created_by',
            'updated_by'
        ];

}
