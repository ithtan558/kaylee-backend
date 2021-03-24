<?php

namespace App\Repositories;

use App\Models\BrandService;
use App\Models\Brand;
use App\Models\User;

class BrandServiceRepository extends BaseRepository
{
    public function __construct(BrandService $model)
    {
        parent::__construct($model);
    }

    public function getByServiceId($serviceId)
    {
        $result = $this->model
            ->select(BrandService::getCol('*'), Brand::getCol('name as brand_name'))
            ->join(Brand::getTbl(), Brand::getCol('id'), '=', BrandService::getCol('brand_id'))
            ->where('service_id', $serviceId)
            ->orderBy('id', 'DESC')
            ->get();

        return $result;
    }

    public function getAll()
    {
        $result = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
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
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->orderBy($order, $sort)
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

    public function deleteByServiceId($serviceId)
    {
        return $this->model
            ->where(BrandService::getCol('service_id'), $serviceId)
            ->delete();
    }

}
