<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Role;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        // Filter base on roles of user
        $user = CommonHelper::getAuth();
        $roles = [];
        $reject_roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }
        if (in_array(ROLE_SUPERADMIN, $roles)) {
            $reject_roles = [ROLE_SUPERADMIN];
        } else if (in_array(ROLE_MANAGER, $roles)) {
            $reject_roles = [ROLE_SUPERADMIN, ROLE_MANAGER];
        } else if (in_array(ROLE_BRAND_MANAGER, $roles)) {
            $reject_roles = [ROLE_SUPERADMIN, ROLE_MANAGER, ROLE_BRAND_MANAGER];
        }

        $result = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE)
            ->whereNotIn('id', $reject_roles)
            ->get();

        return $result;
    }

}
