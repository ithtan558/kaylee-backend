<?php

namespace App\Repositories;

use App\Models\OrderPayment;

class OrderPaymentRepository extends BaseRepository
{
    public function __construct(OrderPayment $model)
    {
        parent::__construct($model);
    }

}
