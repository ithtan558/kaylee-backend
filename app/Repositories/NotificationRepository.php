<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Notification;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [Notification::getCol('title')],
                'compare' => 'like',
                'type'    => 'string',
            ],
        ];
    }

    public function getList($params)
    {
        $order  = 'id';
        $length = $this->getLength($params);
        $sort   = $this->getOrder($params);



        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }
        $query = $this->model
            ->select('id', 'title', 'description', 'content', 'status', 'created_at')
            ->where('is_active', STATUS_ACTIVE);
        $query = $this->addConditionToQuery($query, $params, $this->getFieldSearchAble());

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }

        $query = $query->where('user_id', $user->id);
        $query = $query->where('is_active', STATUS_ACTIVE);
        $query = $query->where('is_delete', STATUS_INACTIVE);

        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select("id", "title", "type", "product_id",  "description", "content")
            ->where('id', $id)
            ->first();

        return $query;
    }

    public function countUnRead()
    {
        $user  = CommonHelper::getAuth();
        return $this->model->where("status", NOTIFICATION_NOT_READ)
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->where('client_id', $user->client_id)
            ->where('user_id', $user->id)
            ->count();
    }


}
