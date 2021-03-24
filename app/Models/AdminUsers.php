<?php

namespace App\Models;

class AdminUsers extends BaseModel
{

    protected $table = 'admin_users';
    protected $fillable
        = [
            'id',
            'username',
            'password',
            'name',
            'avatar',
            'supplier_id',
            'code',
            'remember_token',
            'is_active',
            'is_delete',
            'created_by',
            'updated_by'
        ];

}
