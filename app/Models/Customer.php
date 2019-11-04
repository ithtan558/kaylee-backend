<?php

namespace App\Models;

class Customer extends BaseModel
{

    protected $table = 'customer';
    protected $fillable
        = [
            'client_id',
            'name',
            'phone',
            'email',
            'birthday',
            'image',
            'created_by',
            'updated_by'
        ];

}
