<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderReasonCancelService;
use App\Libraries\Api;

class OrderReasonCancelController extends Controller
{

    protected $request;
    protected $orderReasonCancelService;

    public function __construct(Request $request, OrderReasonCancelService $orderReasonCancelService)
    {
        $this->request     = $request;
        $this->orderReasonCancelService = $orderReasonCancelService;
    }

    public function getAll()
    {
        $data = $this->orderReasonCancelService->getAll($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
