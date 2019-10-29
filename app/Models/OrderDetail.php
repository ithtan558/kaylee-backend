<?php

namespace App\Models;

class OrderDetail extends BaseModel
{

    protected $table = 'order_detail';

    public $timestamps = false;

    protected $fillable
        = [
            'order_id',
            'service_id',
            'name',
            'price',
            'quantity',
            'total',
            'note'
        ];

}
