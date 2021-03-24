<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Order;
use App\Models\OrderEmployee;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderEmployeeRepository extends BaseRepository
{
    public function __construct(OrderEmployee $model)
    {
        parent::__construct($model);
    }

    public function getByOrderId($order_id)
    {
        $data = $this->model
            ->select([
                User::getCol('name'),
                User::getCol('id'),
                Role::getCol('name'). " as role_name",
                Role::getCol('id'). " as role_id",
            ])
            ->join(User::getTbl(), User::getCol("id"), "=", OrderEmployee::getCol("employee_id"))
            ->join(UserRole::getTbl(), UserRole::getCol("user_id"), "=", User::getCol("id"))
            ->join(Role::getTbl(), Role::getCol("id"), "=", UserRole::getCol("role_id"))
            ->where(OrderEmployee::getCol('order_id'), $order_id)
            ->get();

        return $data;
    }

}
