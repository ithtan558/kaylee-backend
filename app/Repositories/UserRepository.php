<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }


    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [User::getCol('name'), User::getCol('phone')],
                'compare' => 'like',
                'type'    => 'string',
            ],
        ];
    }

    public function getAll()
    {
        $result = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE)
            ->orderBy('id', 'DESC')
            ->get();

        return $result;
    }

    public function getList($params)
    {
        $order  = 'id';
        $length = $this->getLength($params);
        $sort   = $this->getOrder($params);

        $query = $this->model
            ->select("*")
            ->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->where('id', $id)
            ->with(['user_roles'])
            ->first();

        $query->role_id = $query->user_roles[0]->role_id;
        unset($query->user_roles);

        return $query;
    }

    public function getByPhoneOrName($params)
    {
        $order = 'id';
        $sort  = $this->getOrder($params);

        $query = $this->model->select(User::getCol('*'));
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

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

        $result = $query->orderBy($order, $sort)->get();

        return $result;
    }

}
