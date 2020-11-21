<?php

namespace App\Models;

class ServiceCategory extends BaseModel
{

    protected $table = 'service_category';
    protected $fillable
        = [
            'client_id',
            'name',
            'description',
            'sequence',
            'code',
            'is_active',
            'is_delete',
            'created_by',
            'updated_by'
        ];

}
