<?php

namespace App\Models;


class OrderEmployee extends BaseModel
{

    protected $table = 'order_employee';

    protected $fillable
        = [
            'client_id',
            'brand_id',
            'employee_id',
            'order_id',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by'
        ];

}
