<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\UserLocation;

class UserLocationRepository extends BaseRepository
{
    public function __construct(UserLocation $model)
    {
        parent::__construct($model);
    }

}
