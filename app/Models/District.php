<?php

namespace App\Models;

class District extends BaseModel
{

    protected $table = 'district';
    protected $fillable
        = [
            'city_id',
            'name',
            'created_by',
            'updated_by'
        ];

}
