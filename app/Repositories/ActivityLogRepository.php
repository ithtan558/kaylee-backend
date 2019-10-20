<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class ActivityLogRepository extends BaseRepository
{
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }

    public function getList($params)
    {
        $length = isset($params['limit']) ? $params['limit'] : 10;
        $order = isset($params['sort']) ? $params['sort'] : ActivityLog::getCol('id');
        $sort = isset($params['order']) ? $params['order'] : 'DESC';

        $query = $this->model
            ->select("*")
            ->orderBy($order, $sort)
            ->with(['user'])
            ->paginate($length);

        return $this->formatPagination($query);
    }

    public function writingAccessLog(array $params)
    {
        $auth = CommonHelper::getAuth();
        $userId = $auth ? $auth->id : 0;

        return $this->model->create([
            'path' => app('request')->path(),
            'user_id' => $userId,
            'method' => app('request')->method(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'params' => json_encode($params, true),
            'created_by' => $userId,
        ]);
    }
}
