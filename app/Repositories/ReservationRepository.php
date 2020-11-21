<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Brand;
use App\Models\Reservation;
use App\Models\User;

class ReservationRepository extends BaseRepository
{
    public function __construct(Reservation $model)
    {
        parent::__construct($model);
    }

    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [Reservation::getCol('first_name'), Reservation::getCol('last_name'), Reservation::getCol('phone')],
                'compare' => 'like',
                'type'    => 'string',
            ],
        ];
    }

    public function getAll()
    {
        // Filter base on rsupplier_idoles of user
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

        $query = $this->model->select(
            Reservation::getCol('id'),
            Reservation::getCol('code'),
            Reservation::getCol('first_name'),
            Reservation::getCol('last_name'),
            Reservation::getCol('status'),
            Reservation::getCol('quantity'),
            Reservation::getCol('datetime'),
            Reservation::getCol('customer_id'),
            Reservation::getCol('brand_id'),
            Brand::getCol('name as brand_name')
        )
        ->join(Brand::getTbl(), Brand::getCol("id"), "=", Reservation::getCol("brand_id"));
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }
        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where(Reservation::getCol('client_id'), $user->client_id);
        }
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        if (!empty($params['brand_id'])) {
            $query = $query->where(Reservation::getCol('brand_id'), $params['brand_id']);
        }

        if (!empty($params['status'])) {
            $query = $query->where(Reservation::getCol('status'), $params['status']);
        }

        if (!empty($params['datetime'])) {
            $query = $query->whereDate(Reservation::getCol('datetime'), '=', $params['datetime']);
        }

        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getByPhoneOrName($params)
    {
        $order = 'id';
        $sort  = $this->getOrder($params);

        $query = $this->model->select(Reservation::getCol('*'));
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
                Reservation::getCol("id"),
                Reservation::getCol("brand_id"),
                Reservation::getCol("code"),
                Reservation::getCol("first_name"),
                Reservation::getCol("last_name"),
                Reservation::getCol("phone"),
                Reservation::getCol("address"),
                Reservation::getCol("city_id"),
                Reservation::getCol("district_id"),
                Reservation::getCol("wards_id"),
                Reservation::getCol("datetime"),
                Reservation::getCol("quantity"),
                Reservation::getCol("note"),
                Brand::getCol("name as brand_name")
            )
            ->join(Brand::getTbl(), Brand::getCol("id"), "=", Reservation::getCol("brand_id"))
            ->where(Reservation::getCol("id"), $id)
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
