<?php

namespace App\Models;

class Campaign extends BaseModel
{

    protected $table = 'campaign';
    protected $fillable
        = [
            'key',
            'content',
            'is_active',
            'created_by',
            'updated_by'
        ];

}
