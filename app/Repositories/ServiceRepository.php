<?php

namespace App\Repositories;

use App\Models\OutletMaster;
use App\Models\Service;

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
                'field'   => Service::getCol('name'),
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

        $query = $this->model->select(Service::getCol('*'));
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        $query = $query->orderBy($order, $sort)
                    ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select("id", "name", "code", "description", "time", "price", "category_id", "image")
            ->where('id', $id)
            ->first();

        return $query;
    }

}
