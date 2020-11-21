<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupplierService;
use App\Libraries\Api;

class SupplierController extends Controller
{

    protected $request;
    protected $supplierService;

    public function __construct(Request $request, SupplierService $supplierService)
    {
        $this->request         = $request;
        $this->supplierService = $supplierService;
    }

    public function getList()
    {
        $data = $this->supplierService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($id)
    {
        $result = $this->supplierService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

}
