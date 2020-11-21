<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\UserDevice;

class UserDeviceRepository extends BaseRepository
{
    public function __construct(UserDevice $model)
    {
        parent::__construct($model);
    }

}
