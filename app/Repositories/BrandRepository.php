<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Brand;
use App\Models\User;

class BrandRepository extends BaseRepository
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {

        // Filter base on roles of user
        $user = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE);

        if (in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('id', $user->brand_id);
        } else if (in_array(ROLE_MANAGER, $roles)){
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

        // Filter base on roles of user
        $user = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE);

        if (in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('id', $user->brand_id);
        } else if (in_array(ROLE_MANAGER, $roles)){
            $query = $query->where('client_id', $user->client_id);
        }


        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select("id", "name", "phone", "location", "start_time", "end_time", "city_id", "district_id", "image")
            ->where('id', $id)
            ->first();

        return $query;
    }

}
