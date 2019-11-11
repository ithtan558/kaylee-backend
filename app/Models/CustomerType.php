<?php

namespace App\Models;

class CustomerType extends BaseModel
{

    protected $table = 'customer_type';

    protected $fillable
        = [
            'code',
            'name',
            'created_by',
            'updated_by'
        ];

}
