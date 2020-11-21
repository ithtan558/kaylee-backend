<?php

namespace App\Models;

class Reservation extends BaseModel
{

    protected $table = 'reservation';
    protected $fillable
        = [
            'client_id',
            'brand_id',
            'customer_id',
            'code',
            'first_name',
            'last_name',
            'city_id',
            'district_id',
            'wards_id',
            'address',
            'phone',
            'email',
            'quantity',
            'note',
            'datetime',
            'status',
            'created_by',
            'updated_by'
        ];

}
