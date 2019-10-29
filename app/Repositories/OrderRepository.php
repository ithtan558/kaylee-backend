<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderDetail;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getList($params)
    {
        $order  = 'id';
        $length = $this->getLength($params);
        $sort   = $this->getOrder($params);

        $query = $this->model->select(Order::getCol('*'));
        $query = $this->addConditionToQuery($query, $params, []);
        $query->with(['order_details']);

        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);

    }

}
