<?php

namespace App\Models;

use App\Models\OrderDetail;

class Order extends BaseModel
{

    protected $table = 'order';

    protected $fillable
        = [
            'brand_id',
            'employee_id',
            'code',
            'customer_id',
            'order_status_id',
            'is_paid',
            'name',
            'phone',
            'email',
            'note',
            'amount'
        ];

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

}
