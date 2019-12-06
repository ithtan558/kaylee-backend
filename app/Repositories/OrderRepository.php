<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getList($params)
    {
        $order  = 'id';
        $length = $this->getLength($params);
        $sort   = $this->getOrder($params);

        $query = $this->model->select(Order::getCol('*'));
        $query = $this->addConditionToQuery($query, $params, []);
        $query->with(['order_details']);

        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        }

        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);

    }

    public function getAll()
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE);

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        }

        $result = $query->orderBy('id', 'DESC')->get();

        return $result;
    }

    public function getCount()
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*');

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        }

        $result = $query->count();

        return $result;
    }

    public function getTotal($params)
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*');

        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }
        if (isset($params['brand_id']) && $params['brand_id']) {
            $query = $query->where('brand_id', $params['brand_id']);
        }

        if (isset($params['start_date'])) {
            $query->whereRaw('date(created_at) between "' . $params['start_date'] . '" and "' . $params['end_date'] . '"');
        }

        $result = $query->orderBy('id', 'DESC')->get();

        $total_value = 0;
        foreach ($result as $item) {
            $total_value+= $item->amount;
        }


        return ['total_value' => $total_value];
    }

    public function getTotalByEmployeeAndDate($params)
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select(
                DB::raw('SUM('.Order::getCol('amount').') as amount'),
                User::getCol('name'),
                User::getCol('phone')
            )
            ->from(Order::getTbl())
            ->join(User::getTbl(), User::getCol("id"), "=", Order::getCol("employee_id"));

        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where(Order::getCol('client_id'), $user->client_id);
        }
        if (isset($params['brand_id']) && $params['brand_id']) {
            $query = $query->where(Order::getCol('brand_id'), $params['brand_id']);
        }

        $query->whereRaw('date('.Order::getCol('created_at').') between "'.$params['start_date'].'" and "'.$params['end_date'].'"');

        $result = $query->orderBy(Order::getCol('id'), 'DESC')
            ->groupBy(Order::getCol('employee_id'))->get();

        return $result;

    }

    public function getTotalByServiceAndDate($params)
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select(
                DB::raw('SUM('.Order::getCol('amount').') as amount'),
                Service::getCol('name')
            )
            ->from(OrderDetail::getTbl())
            ->join(Order::getTbl(), Order::getCol("id"), "=", OrderDetail::getCol("order_id"))
            ->join(Service::getTbl(), Service::getCol("id"), "=", OrderDetail::getCol("service_id"));

        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where(Order::getCol('client_id'), $user->client_id);
        }
        if (isset($params['brand_id']) && $params['brand_id']) {
            $query = $query->where(Order::getCol('brand_id'), $params['brand_id']);
        }

        $query->whereRaw('date('.Order::getCol('created_at').') between "'.$params['start_date'].'" and "'.$params['end_date'].'"');

        $result = $query->orderBy(Order::getCol('id'), 'DESC')
            ->groupBy(OrderDetail::getCol('service_id'))->get();

        return $result;

    }

}
