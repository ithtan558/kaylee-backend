<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Brand;
use App\Models\User;
use App\Models\Version;

class VersionRepository extends BaseRepository
{
    public function __construct(Version $model)
    {
        parent::__construct($model);
    }

    public function getDetail()
    {
        $query = $this->model
            ->select(["name", "code", "description", "force"])
            ->where("is_active", STATUS_ACTIVE)
            ->first();
        return $query;
    }

}
