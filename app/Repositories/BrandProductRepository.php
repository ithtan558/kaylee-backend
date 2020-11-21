<?php

namespace App\Repositories;

use App\Models\BrandProduct;
use App\Models\Brand;
use App\Models\User;

class BrandProductRepository extends BaseRepository
{
    public function __construct(BrandProduct $model)
    {
        parent::__construct($model);
    }

    public function getByProductId($productId)
    {
        $result = $this->model
            ->select(BrandProduct::getCol('*'), Brand::getCol('name as brand_name'))
            ->join(Brand::getTbl(), Brand::getCol('id'), '=', BrandProduct::getCol('brand_id'))
            ->where('product_id', $productId)
            ->orderBy('id', 'DESC')
            ->get();

        return $result;
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
            ->select("id", "name", "phone", "location", "start_time", "end_time", "city_id", "district_id", "image")
            ->where('id', $id)
            ->first();

        return $query;
    }

    public function deleteByProductId($productId)
    {
        return $this->model
            ->where(BrandProduct::getCol('product_id'), $productId)
            ->delete();
    }

}
