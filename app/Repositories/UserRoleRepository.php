<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;

class UserRoleRepository extends BaseRepository
{
    public function __construct(UserRole $model)
    {
        parent::__construct($model);
    }

    /*
    * @Params $userId
    * @Response list array user role
    * */
    public function getRoleByUserId($userId)
    {
        return $this->model
            ->select(
                UserRole::getCol("user_id"),
                UserRole::getCol("role_id"),
                Role::getCol("code")
            )
            ->join(Role::getTbl(), Role::getCol("id"), "=", UserRole::getCol("role_id"))
            ->where(UserRole::getCol("user_id"), $userId)
            ->get();
    }
}
