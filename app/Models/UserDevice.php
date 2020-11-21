<?php

namespace App\Models;

class UserDevice extends BaseModel
{

    protected $table = 'user_device';
    protected $fillable
        = [
            'client_id',
            'user_id',
            'device_id',
            'token',
            'created_by',
            'updated_by'
        ];

}
