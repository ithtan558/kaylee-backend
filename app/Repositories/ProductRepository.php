<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\BrandProduct;
use App\Models\BrandService;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [Product::getCol('name')],
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
            Product::getCol('id'),
            Product::getCol('name'),
            Product::getCol('image'),
            Product::getCol('price'),
            Product::getCol('supplier_id')
        ]);
        if (isset($params['brand_ids'])) {
            $arr = explode(',', $params['brand_ids']);
            $query = $query->join(BrandProduct::getTbl(), BrandProduct::getCol('product_id'), '=', Product::getCol('id'));
            $query = $query->whereIn(BrandProduct::getCol('brand_id'), $arr);
        }
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        if (!isset($params['supplier_id']) && (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles))) {
            $query = $query->where(Product::getCol('client_id'), $user->client_id);
        } else {
            $query = $query->where(Product::getCol('supplier_id'), $params['supplier_id']);
        }

        if (isset($params['category_id'])) {
            $query = $query->where(Product::getCol('category_id'), $params['category_id']);
        }

        if (isset($params['start_price']) && isset($params['end_price'])) {
            $query = $query->where(Product::getCol('price'), ">=", $params['start_price']);
            $query = $query->where(Product::getCol('price'), "<=", $params['end_price']);
        }

        $query = $query->where(Product::getCol('is_active'), STATUS_ACTIVE);
        $query = $query->where(Product::getCol('is_delete'), STATUS_INACTIVE);

        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select(
                Product::getCol('id'),
                Product::getCol('code'),
                Product::getCol('name'),
                Product::getCol('image'),
                Product::getCol('image1'),
                Product::getCol('image2'),
                Product::getCol('image3'),
                Product::getCol('image4'),
                Product::getCol('video'),
                Product::getCol('video1'),
                Product::getCol('video2'),
                Product::getCol('video3'),
                Product::getCol('video4'),
                Product::getCol('price'),
                Product::getCol('category_id'),
                Product::getCol('description'),
                ProductCategory::getCol('name as category_name'),
                Product::getCol('supplier_id')
            )
            ->join(ProductCategory::getTbl(), ProductCategory::getCol('id'), '=', Product::getCol('category_id'))
            ->where(Product::getCol('id'), $id)
            ->first();

        return $query;
    }

}
