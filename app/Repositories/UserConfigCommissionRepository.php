<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\UserConfigCommission;

class UserConfigCommissionRepository extends BaseRepository
{
    public function __construct(UserConfigCommission $model)
    {
        parent::__construct($model);
    }

    public function getByUserId($user_id)
    {
        $result = $this->model
            ->select('commission_product', 'commission_service', )
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->where('user_id', $user_id)
            ->first();

        return $result;
    }

}
