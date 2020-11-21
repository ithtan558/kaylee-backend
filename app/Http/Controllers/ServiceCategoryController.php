<?php

namespace App\Http\Controllers;

use App\Http\Validators\ServiceCategory\ProductCategoryCreateValidator;
use App\Http\Validators\ServiceCategory\ProductCategoryUpdateValidator;
use App\Http\Validators\ServiceCategory\ServiceCategoryCreateValidator;
use App\Http\Validators\ServiceCategory\ServiceCategoryUpdateValidator;
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

    public function getList()
    {
        $data = $this->serviceCategory->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($id)
    {
        $result = $this->serviceCategory->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

    public function create()
    {
        $this->validate($this->request, ServiceCategoryCreateValidator::rules(), ServiceCategoryCreateValidator::messages());

        $data = $this->serviceCategory->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function update()
    {
        $this->validate($this->request, ServiceCategoryUpdateValidator::rules(), ServiceCategoryUpdateValidator::messages());

        $data = $this->serviceCategory->update($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function delete($id)
    {
        $data = $this->serviceCategory->delete($id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
