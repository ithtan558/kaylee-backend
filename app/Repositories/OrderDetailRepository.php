<?php

namespace App\Repositories;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Service;

class OrderDetailRepository extends BaseRepository
{
    public function __construct(OrderDetail $model)
    {
        parent::__construct($model);
    }

    public function getList($params)
    {
        $order  = 'id';
        $sort   = $this->getOrder($params);

        $query = $this->model->select(OrderDetail::getCol('*'));
        $query = $this->addConditionToQuery($query, $params, []);

        $data = $query->orderBy($order, $sort)->get();

        return $data;
    }

    public function getByOrderId($order_id)
    {
        $data = $this->model->select([
            OrderDetail::getCol('service_id'),
            OrderDetail::getCol('product_id'),
            OrderDetail::getCol('quantity'),
            OrderDetail::getCol('price'),
            OrderDetail::getCol('total'),
            OrderDetail::getCol('name'),
            OrderDetail::getCol('note')
        ])
            ->where(OrderDetail::getCol('order_id'), $order_id)
            ->get();

        return $data;
    }
}
