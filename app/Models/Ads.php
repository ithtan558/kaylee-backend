<?php

namespace App\Models;

class Ads extends BaseModel
{

    protected $table = 'ads';
    protected $fillable
        = [
            'client_id',
            'title',
            'description',
            'image',
            'url',
            'type',
            'start_date',
            'end_date',
            'is_active',
            'is_delete',
            'created_by',
            'updated_by'
        ];

}
