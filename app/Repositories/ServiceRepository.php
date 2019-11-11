<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\OutletMaster;
use App\Models\Service;

class ServiceRepository extends BaseRepository
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }

    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [Service::getCol('name')],
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

        $query = $this->model->select(Service::getCol('*'));
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

        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select("id", "name", "code", "description", "time", "price", "category_id", "image")
            ->where('id', $id)
            ->first();

        return $query;
    }

}
