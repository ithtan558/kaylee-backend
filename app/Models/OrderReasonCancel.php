<?php

namespace App\Models;

class OrderReasonCancel extends BaseModel
{

    protected $table = 'order_reason_cancel';
    protected $fillable
        = [
            'name',
            'code',
            'type',
            'is_active',
            'created_by',
            'updated_by'
        ];

}
