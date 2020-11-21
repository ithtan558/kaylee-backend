<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Customer;

class CustomerRepository extends BaseRepository
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [Customer::getCol('first_name'), Customer::getCol('last_name'), Customer::getCol('phone')],
                'compare' => 'like',
                'type'    => 'string',
            ],
        ];
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
            $query = $query->where('client_id', $user->client_id);
        }

        $result = $query->orderBy('id', 'DESC')->get();

        return $result;
    }

    public function getList($params)
    {
        $order  = 'id';
        $length = $this->getLength($params);
        $sort   = $this->getOrder($params);

        $query = $this->model->select(["id", "first_name", "last_name", "phone", "image"]);
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }
        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {

            $query = $query->where('client_id', $user->client_id);
        }
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        if (!empty($params['city_id'])) {
            $query = $query->where('city_id', $params['city_id']);
        }
        if (!empty($params['district_ids'])) {

            $arr = explode(',', $params['district_ids']);
            $query = $query->whereIn('district_id', $arr);
        }
        if (!empty($params['type_id'])) {
            $query = $query->where('type_id', $params['type_id']);
        }

        $query = $query->where('is_active', STATUS_ACTIVE);
        $query = $query->where('is_delete', STATUS_INACTIVE);

        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getByPhoneOrName($params)
    {
        $order = 'id';
        $sort  = $this->getOrder($params);

        $query = $this->model->select(["id", "first_name", "last_name", "phone"]);
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }

        $result = $query->orderBy($order, $sort)->get();

        return $result;
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select(
                "id",
                "name",
                "phone",
                "image",
                "first_name",
                "last_name",
                "hometown_city_id",
                "address",
                "city_id",
                "district_id",
                "wards_id",
                "birthday",
                "email"
            )
            ->where('id', $id)
            ->first();

        return $query;
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
            ->select('*')
            ->where('is_active', STATUS_ACTIVE);

        if (in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            //$query = $query->where('brand_id', $user->brand_id);
        } else if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }

        $result = $query->count();

        return $result;
    }

}
