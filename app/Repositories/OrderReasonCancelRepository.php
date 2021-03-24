<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\OrderReasonCancel;

class OrderReasonCancelRepository extends BaseRepository
{
    public function __construct(OrderReasonCancel $model)
    {
        parent::__construct($model);
    }

    public function getAll($type)
    {

        $result = $this->model
            ->select(['id', 'name', 'code'])
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->where('type', $type)
            ->get();

        return $result;
    }

}
