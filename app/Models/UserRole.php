<?php

namespace App\Models;

class UserRole extends BaseModel
{

    protected $table = 'user_role';
    protected $fillable = [
        'id',
        'user_id',
        'role_id',
        'created_by',
        'updated_by'
    ];

}
