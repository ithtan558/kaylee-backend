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
            ->where('is_delete', STATUS_INACTIVE)
            ->first();
        return $query;
    }

    public function checkVersion($version_code)
    {
        $query = $this->model
            ->select(["name", "code", "description", "force"])
            ->where("is_active", STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->where('code', '>',  $version_code)
            ->first();
        return $query;
    }

}
