<?php

namespace App\Models;

class UserAdvise extends BaseModel
{

    protected $table = 'user_advise';
    protected $fillable
        = [
            'id',
            'name',
            'phone',
            'email',
            'content',
            'is_active',
            'is_delete',
            'created_by',
            'updated_by'
        ];

}
