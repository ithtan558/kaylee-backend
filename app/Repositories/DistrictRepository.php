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
            ->select('id', 'name', 'city_id')
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->get();

        return $result;
    }

    public function getListByCity($city_id)
    {
        $result = $this->model
            ->select('id', 'name','city_id')
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->where('city_id', $city_id)
            ->get();

        return $result;
    }

}
