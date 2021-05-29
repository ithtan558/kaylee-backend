<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use stdClass;

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
            ->where('is_delete', STATUS_INACTIVE)
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

        $query = $this->model->select("id", "name", "image");
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }
        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        } else if (in_array(ROLE_BRAND_MANAGER, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        }

        if (!empty($params['brand_id'])) {
            $query = $query->where('brand_id', $params['brand_id']);
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

    public function getDetail($id)
    {
        $query = $this->model
            ->select(
                User::getCol('id'),
                User::getCol('name'),
                User::getCol('birthday'),
                User::getCol('address'),
                User::getCol('hometown_city_id'),
                User::getCol('city_id'),
                User::getCol('district_id'),
                User::getCol('wards_id'),
                User::getCol('phone'),
                User::getCol('email'),
                User::getCol('brand_id'),
                Brand::getCol('name as brand_name'),
                User::getCol('image')
            )
            ->join(Brand::getTbl(), Brand::getCol("id"), "=", User::getCol("brand_id"))
            ->where(User::getCol("id"), $id)
            ->where(User::getCol("is_delete"), STATUS_INACTIVE)
            ->with(['user_roles'])
            ->first();

        $user_role   = $query->user_roles[0];
        $obj         = new stdClass();
        $obj->id     = $user_role->role->id;
        $obj->name   = $user_role->role->name;
        $query->role = $obj;
        unset($query->user_roles);

        return $query;
    }

    public function verifyByPhone($phone)
    {
        $query = $this->model
            ->where('phone', $phone)
            ->where('is_delete', STATUS_INACTIVE)
            ->first();

        return $query;
    }

    public function getByPhoneOrName($params)
    {
        $order = 'id';
        $sort  = $this->getOrder($params);

        $query = $this->model->select([
            User::getCol('id'),
            User::getCol('brand_id'),
            User::getCol('name'),
            User::getCol('phone'),
            User::getCol('image')
        ]);
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        if (in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        } else if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }

        if (isset($params['brand_id'])) {
            $query = $query->where('brand_id', $params['brand_id']);
        }

        $query->where('is_delete', STATUS_INACTIVE);
        $query->where('is_delete', STATUS_INACTIVE);
        $query->with('user_roles');
        $result = $query->orderBy($order, $sort)->get();

        return $result;
    }

    public function getAllUnBlock()
    {
        $result = $this->model
            ->select('*')
            ->where('is_delete', STATUS_INACTIVE)
            ->where('is_active', STATUS_ACTIVE)
            ->orderBy('id', 'DESC')
            ->get();

        return $result;
    }

}
