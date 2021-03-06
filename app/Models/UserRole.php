<?php

namespace App\Models;

class UserRole extends BaseModel
{

    protected $table = 'user_role';
    protected $fillable
        = [
            'id',
            'user_id',
            'role_id',
            'is_active',
            'is_delete',
            'created_by',
            'updated_by'
        ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
