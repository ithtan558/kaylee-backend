<?php

namespace App\Models;

class Notification extends BaseModel
{

    protected $table = 'notification';
    protected $fillable
        = [
            'client_id',
            'user_id',
            'type',
            'product_id',
            'title',
            'description',
            'content',
            'status',
            'is_active',
            'is_delete',
        ];

}
