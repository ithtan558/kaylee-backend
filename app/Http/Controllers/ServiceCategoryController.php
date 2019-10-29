<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ServiceCategoryService;
use App\Libraries\Api;

class ServiceCategoryController extends Controller
{

    protected $request;
    protected $serviceCategory;

    public function __construct(Request $request, ServiceCategoryService $serviceCategory)
    {
        $this->request         = $request;
        $this->serviceCategory = $serviceCategory;
    }

    public function getAll()
    {
        $data = $this->serviceCategory->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
