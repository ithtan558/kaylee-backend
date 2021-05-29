<?php

namespace App\Http\Controllers;

use App\Http\Validators\Order\OrderCreateValidator;
use App\Http\Validators\Order\OrderUpdateStatusValidator;
use App\Http\Validators\Order\OrderUpdateValidator;
use App\Http\Validators\Order\SupplierOrderCreateValidator;
use App\Http\Validators\Order\SupplierOrderUpdateValidator;
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

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getCount()
    {
        $data = $this->orderService->getCount();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getList()
    {
        $data = $this->orderService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getDetail($id)
    {
        $result = $this->orderService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY], $this->request);
    }

    public function create()
    {
        $this->validate($this->request, OrderCreateValidator::rules(), OrderCreateValidator::messages());
        $data = $this->orderService->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function update()
    {
        $this->validate($this->request, OrderUpdateValidator::rules(), OrderUpdateValidator::messages());
        $data = $this->orderService->update($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function createSupplier()
    {
        $this->validate($this->request, SupplierOrderCreateValidator::rules(), SupplierOrderCreateValidator::messages());
        $data = $this->orderService->createSupplier($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function updateSupplier()
    {
        $this->validate($this->request, SupplierOrderUpdateValidator::rules(), SupplierOrderUpdateValidator::messages());
        $data = $this->orderService->updateSupplier($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function updateStatus()
    {
        $this->validate($this->request, OrderUpdateStatusValidator::rules(), OrderUpdateStatusValidator::messages());
        $data = $this->orderService->updateStatus($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }
}
