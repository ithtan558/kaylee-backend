<?php

namespace App\Repositories;

use App\Models\ServiceCategory;

class ServiceCategoryRepository extends BaseRepository
{
    public function __construct(ServiceCategory $model)
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

}
