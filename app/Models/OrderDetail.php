<?php

namespace App\Models;

class OrderDetail extends BaseModel
{

    protected $table = 'order_detail';

    public $timestamps = false;

    protected $fillable
        = [
            'client_id',
            'order_id',
            'service_id',
            'product_id',
            'name',
            'price',
            'quantity',
            'total',
            'note',
            'is_active',
            'is_delete'
        ];

}
