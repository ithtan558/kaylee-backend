<?php

namespace App\Repositories;

use App\Models\Wards;

class WardsRepository extends BaseRepository
{
    public function __construct(Wards $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        $result = $this->model
            ->select('id', 'name', 'city_id', 'district_id')
            ->where('is_active', STATUS_ACTIVE)
            ->get();

        return $result;
    }

    public function getListByDistrict($district_id)
    {
        $result = $this->model
            ->select('id', 'name', 'city_id', 'district_id')
            ->where('is_active', STATUS_ACTIVE)
            ->where('district_id', $district_id)
            ->get();

        return $result;
    }

}
