<?php

namespace App\Models;

class Customer extends BaseModel
{

    protected $table = 'customer';
    protected $fillable
        = [
            'name',
            'phone',
            'email',
            'birthday',
            'image',
            'created_by',
            'updated_by'
        ];

}
