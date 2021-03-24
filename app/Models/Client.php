<?php

namespace App\Models;

class Client extends BaseModel
{

    protected $table = 'client';

    public $timestamps = false;

    protected $fillable
        = [
            'admin_user_id',
            'name',
            'phone',
            'location',
            'city_id',
            'district_id',
            'wards_id',
            'is_active',
            'is_delete',
            'created_at'
        ];

}
