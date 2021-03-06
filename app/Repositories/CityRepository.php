<?php

namespace App\Repositories;

use App\Models\City;

class CityRepository extends BaseRepository
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        $result = $this->model
            ->select('id', 'name')
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->orderBy('name', 'DESC')
            ->get();

        return $result;
    }

}
