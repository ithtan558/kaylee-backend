<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Brand;
use App\Models\BrandService;
use App\Models\OutletMaster;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;

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
            ->select(['id', 'name', 'image', 'price'])
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE);

        if (in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('id', $user->brand_id);
        } else if (in_array(ROLE_MANAGER, $roles)) {
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

        $query = $this->model->select([
            Service::getCol('id'),
            Service::getCol('name'),
            Service::getCol('image'),
            Service::getCol('price')
        ]);
        if (isset($params['brand_ids'])) {
            $arr = explode(',', $params['brand_ids']);
            $query = $query->join(BrandService::getTbl(), BrandService::getCol('service_id'), '=', Service::getCol('id'));
            $query = $query->whereIn(BrandService::getCol('brand_id'), $arr);
        }
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where(Service::getCol('client_id'), $user->client_id);
        }

        if (isset($params['category_id'])) {
            $query = $query->where(Service::getCol('category_id'), $params['category_id']);
        }

        if (isset($params['start_price']) && isset($params['end_price'])) {
            $query = $query->where(Service::getCol('price'), ">=", $params['start_price']);
            $query = $query->where(Service::getCol('price'), "<=", $params['end_price']);
        }

        $query = $query->where(Service::getCol('is_active'), STATUS_ACTIVE);
        $query = $query->where(Service::getCol('is_delete'), STATUS_INACTIVE);

        $query = $query->orderBy($order, $sort)
            ->groupBy(Service::getCol('id'))
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select(
                Service::getCol('id'),
                Service::getCol('code'),
                Service::getCol('name'),
                Service::getCol('time'),
                Service::getCol('price'),
                Service::getCol('description'),
                Service::getCol('category_id'),
                Service::getCol('image'),
                ServiceCategory::getCol('name as category_name')
            )
            ->join(ServiceCategory::getTbl(), ServiceCategory::getCol('id'), '=', Service::getCol('category_id'))
            ->where(Service::getCol('id'), $id)
            ->first();

        return $query;
    }

    public function getByCaterogyIds($service_category_ids, $client_id)
    {
        $query = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE)
            ->where('client_id', $client_id)
            ->where('is_delete', STATUS_INACTIVE);

        $query = $query->whereIn('category_id', $service_category_ids);

        $result = $query->orderBy('id', 'DESC')->get();

        return $result;
    }

}
