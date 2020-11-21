<?php

namespace App\Models;

class UserConfigCommission extends BaseModel
{

    protected $table = 'user_config_commission';
    protected $fillable
        = [
            'client_id',
            'brand_id',
            'user_id',
            'commission_product',
            'commission_service',
            'is_active',
            'created_by',
            'updated_by'
        ];

}
