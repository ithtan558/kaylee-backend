<?php

namespace App\Models;

class Otp extends BaseModel
{

    protected $table = 'otp';
    protected $fillable
        = [
            'otp',
            'user_id',
            'tried',
            'is_verify',
            'type'
        ];

}
