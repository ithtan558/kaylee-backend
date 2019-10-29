<?php

namespace App\Repositories;

use App\Models\OrderDetail;

class OrderDetailRepository extends BaseRepository
{
    public function __construct(OrderDetail $model)
    {
        parent::__construct($model);
    }

    public function getList($params)
    {
        $order  = 'id';
        $length = $this->getLength($params);
        $sort   = $this->getOrder($params);

        $query = $this->model->select(OrderDetail::getCol('*'));
        $query = $this->addConditionToQuery($query, $params, []);

        $data = $query->orderBy($order, $sort)->get();

        return $data;
    }

}
