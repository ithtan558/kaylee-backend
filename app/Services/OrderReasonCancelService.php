<?php

namespace App\Services;

use App\Repositories\OrderReasonCancelRepository;
use Illuminate\Http\Request;

class OrderReasonCancelService extends BaseService
{
    protected $orderReasonCancelRep;

    public function __construct(OrderReasonCancelRepository $orderReasonCancelRep)
    {
        $this->orderReasonCancelRep = $orderReasonCancelRep;
    }

    public function getAll(Request $request)
    {

        $data = $this->orderReasonCancelRep->getAll($request['type']);

        $this->setData($data);

        return $this->getResponseData();
    }

}
