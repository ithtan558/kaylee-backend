<?php

namespace App\Models;

class Customer extends BaseModel
{

    protected $table = 'customer';
    protected $fillable
        = [
            'client_id',
            'type_id',
            'name',
            'birthday',
            'hometown_city_id',
            'city_id',
            'district_id',
            'wards_id',
            'address',
            'phone',
            'email',
            'birthday',
            'image',
            'is_active',
            'is_delete',
            'created_by',
            'updated_by'
        ];

}
