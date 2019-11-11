<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerTypeService;
use App\Libraries\Api;

class CustomerTypeController extends Controller
{

    protected $request;
    protected $customerTypeService;

    public function __construct(Request $request, CustomerTypeService $customerTypeService)
    {
        $this->request      = $request;
        $this->customerTypeService = $customerTypeService;
    }

    public function getAll()
    {
        $data = $this->customerTypeService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
