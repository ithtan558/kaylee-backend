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
            ->select('*')
            ->where('is_active', STATUS_ACTIVE);

        $result = $query->orderBy('id', 'DESC')->get();

        return $result;
    }

}
