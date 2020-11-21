<?php

namespace App\Models;

class Role extends BaseModel
{

    protected $table = 'role';
    protected $fillable
        = [
            'name',
            'code',
            'description',
            'is_active',
            'created_by',
            'updated_by'
        ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by')->select(['id', 'full_name', 'email']);
    }

    public function user_updated()
    {
        return $this->hasOne(User::class, 'id', 'updated_by')->select(['id', 'full_name', 'email']);
    }
}
