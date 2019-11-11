<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Order;
use App\Models\User;

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

        if (in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        } else if (in_array(ROLE_MANAGER, $roles)){
            $query = $query->where('client_id', $user->client_id);
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

        if (in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        } else if (in_array(ROLE_MANAGER, $roles)){
            $query = $query->where('client_id', $user->client_id);
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

        if (in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        } else if (in_array(ROLE_MANAGER, $roles)){
            $query = $query->where('client_id', $user->client_id);
        }

        $result = $query->count();

        return $result;
    }

}
