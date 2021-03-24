<?php

namespace App\Models;

use App\Models\OrderDetail;

class Order extends BaseModel
{

    protected $table = 'order';

    protected $fillable
        = [
            'client_id',
            'brand_id',
            'employee_id',
            'code',
            'customer_id',
            'supplier_id',
            'order_status_id',
            'order_reason_cancel_id',
            'is_paid',
            'name',
            'phone',
            'email',
            'note',
            'amount',
            'discount',
            'commission',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by'
        ];

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

}
