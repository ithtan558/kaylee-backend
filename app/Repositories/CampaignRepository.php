<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Campaign;

class CampaignRepository extends BaseRepository
{
    public function __construct(Campaign $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        // Filter base on roles of user
        $user         = CommonHelper::getAuth();
        $roles        = [];
        $reject_roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $result = $this->model
            ->select(['id', 'key', 'content'])
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->get();

        return $result;
    }

}
