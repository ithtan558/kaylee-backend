<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\CustomerType;

class CustomerTypeRepository extends BaseRepository
{
    public function __construct(CustomerType $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {

        $query = $this->model
            ->select(["id", "code", "name"])
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE);

        $result = $query->orderBy('id', 'DESC')->get();

        return $result;
    }

}
