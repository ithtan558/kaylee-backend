<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\ServiceCategory;

class ServiceCategoryRepository extends BaseRepository
{
    public function __construct(ServiceCategory $model)
    {
        parent::__construct($model);
    }

    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [ServiceCategory::getCol('name')],
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
            ->select('id', 'code', 'name', 'description', 'sequence', 'image')
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE);

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }

        $result = $query->orderBy('sequence', 'ASC')->get();

        return $result;
    }

    public function getList($params)
    {
        $order  = 'sequence';
        $length = $this->getLength($params);
        $sort   = $this->getOrder($params);

        $query = $this->model->select('id', 'code', 'name', 'description', 'sequence', 'image');
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }

        $query = $query->where('is_active', STATUS_ACTIVE);
        $query = $query->where('is_delete', STATUS_INACTIVE);
        $query = $query->orderBy($order, 'ASC')
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select("id", "name", "description", "code","sequence", "image")
            ->where('id', $id)
            ->first();

        return $query;
    }


}
