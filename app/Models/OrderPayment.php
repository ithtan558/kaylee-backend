<?php

namespace App\Models;

class OrderPayment extends BaseModel
{

    protected $table = 'order_payment';

    public $timestamps = false;

    protected $fillable
        = [
            'order_id',
            'payment_method_id',
            'value',
            'change',
            'total_payment',
            'note'
        ];

}
