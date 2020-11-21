<?php

namespace App\Models;

class Version extends BaseModel
{

    protected $table = 'version';
    protected $fillable
        = [
            'name',
            'code',
            'description',
            'force',
            'is_active',
            'created_by',
            'updated_by'
        ];

}
