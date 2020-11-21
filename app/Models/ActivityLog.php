<?php

namespace App\Models;

class ActivityLog extends BaseModel
{

    protected $table = 'activity_log';
    protected $fillable
        = [
            'user_id',
            'path',
            'method',
            'ip',
            'params',
            'created_by',
            'updated_by'
        ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function user_updated()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
