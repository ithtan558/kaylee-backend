<?php

namespace App\Models;

class UserLocation extends BaseModel
{

    protected $table = 'user_location';
    protected $fillable
        = [
            'client_id',
            'order_id',
            'name',
            'address',
            'wards_id',
            'district_id',
            'city_id',
            'note',
            'created_by',
            'updated_by'
        ];

}
