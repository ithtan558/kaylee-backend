<?php

namespace App\Models;

class Client extends BaseModel
{

    protected $table = 'client';

    public $timestamps = false;

    protected $fillable
        = [
            'name',
            'phone',
            'location',
            'city_id',
            'district_id',
            'wards_id',
            'created_at'
        ];

}
