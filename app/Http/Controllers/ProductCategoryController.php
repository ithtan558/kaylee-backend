<?php

namespace App\Http\Controllers;

use App\Http\Validators\ProductCategory\ProductCategoryCreateValidator;
use App\Http\Validators\ProductCategory\ProductCategoryUpdateValidator;
use Illuminate\Http\Request;
use App\Services\ProductCategoryService;
use App\Libraries\Api;

class ProductCategoryController extends Controller
{

    protected $request;
    protected $productCategory;

    public function __construct(Request $request, ProductCategoryService $productCategory)
    {
        $this->request         = $request;
        $this->productCategory = $productCategory;
    }

    public function getAll()
    {
        $data = $this->productCategory->getAll($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getList()
    {
        $data = $this->productCategory->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getDetail($id)
    {
        $result = $this->productCategory->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY], $this->request);
    }

    public function create()
    {
        $this->validate($this->request, ProductCategoryCreateValidator::rules(), ProductCategoryCreateValidator::messages());

        $data = $this->productCategory->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function update()
    {
        $this->validate($this->request, ProductCategoryUpdateValidator::rules(), ProductCategoryUpdateValidator::messages());

        $data = $this->productCategory->update($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function delete($id)
    {
        $data = $this->productCategory->delete($id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
