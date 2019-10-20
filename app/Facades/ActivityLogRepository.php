<?php

namespace App\Facades;

use App\Repositories\ActivityLogRepository as ActivityLogRep;
use Illuminate\Support\Facades\Facade;

class ActivityLogRepository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ActivityLogRep::class;
    }
}
