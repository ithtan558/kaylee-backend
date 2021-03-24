<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Ads;

class AdsRepository extends BaseRepository
{
    public function __construct(Ads $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        $query = $this->model
            ->select('id', 'title', 'image', 'description', 'url', 'type')
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE);

        $result = $query->orderBy('id', 'DESC')->get()->toArray();

        return $result;
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select("id", "name", "phone", "location", "start_time", "end_time", "city_id", "district_id", "wards_id", "image")
            ->where('id', $id)
            ->first();

        return $query;
    }

}
