<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository extends BaseRepository
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [Customer::getCol('name'), Customer::getCol('phone')],
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

        $query = $this->model->select(Customer::getCol('*'));
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getByPhone($params)
    {
        $order = 'id';
        $sort  = $this->getOrder($params);

        $query = $this->model->select(Customer::getCol('*'));
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        $result = $query->orderBy($order, $sort)->get();

        return $result;
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select("id", "name", "phone", "email", "birthday", "image")
            ->where('id', $id)
            ->first();

        return $query;
    }

}
