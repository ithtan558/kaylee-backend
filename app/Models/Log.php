<?php

namespace App\Models;

class Log extends BaseModel
{

    protected $table = 'log';
    protected $fillable
        = [
            'ip',
            'url',
            'request',
            'response',
            'created_at'
        ];

}
