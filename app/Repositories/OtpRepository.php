<?php

namespace App\Repositories;

use App\Models\Otp;

class OtpRepository extends BaseRepository
{
    public function __construct(Otp $model)
    {
        parent::__construct($model);
    }

    public function verifyByUser($user_id)
    {
        $query = $this->model
            ->where('user_id', $user_id)
            ->first();

        return $query;
    }

    public function verifyByOtp($params, $type)
    {
        $query = $this->model
            ->where('user_id', $params['user_id'])
            ->where('otp', $params['otp'])
            ->where('is_verify', STATUS_INACTIVE)
            ->where('type', $type)
            ->first();

        return $query;
    }

}
