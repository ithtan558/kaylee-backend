<?php

namespace App\Http\Controllers;

use App\Http\Validators\Order\OrderCreateValidator;
use App\Http\Validators\Order\ReportTotalValidator;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Libraries\Api;

class OrderController extends Controller
{

    protected $request;
    protected $orderService;

    public function __construct(Request $request, OrderService $orderService)
    {
        $this->request      = $request;
        $this->orderService = $orderService;
    }

    public function getAll()
    {
        $data = $this->orderService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getCount()
    {
        $data = $this->orderService->getCount();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getList()
    {
        $data = $this->orderService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($id)
    {
        $result = $this->orderService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

    public function create()
    {
        $this->validate($this->request, OrderCreateValidator::rules(), OrderCreateValidator::messages());
        $data = $this->orderService->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
