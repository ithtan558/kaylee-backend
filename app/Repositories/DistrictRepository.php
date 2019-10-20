<?php

namespace App\Repositories;

use App\Models\District;

class DistrictRepository extends BaseRepository
{
    public function __construct(District $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        $result = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE)
            ->orderBy('id', 'DESC')
            ->get();

        return $result;
    }

    public function getListByCity($city_id)
    {
        $result = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE)
            ->where('city_id', $city_id)
            ->orderBy('id', 'DESC')
            ->get();

        return $result;
    }

}
