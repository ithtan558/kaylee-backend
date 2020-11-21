<?php

namespace App\Http\Controllers;

use App\Http\Validators\Product\ProductCreateValidator;
use App\Http\Validators\Product\ProductUpdateValidator;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Libraries\Api;

class ProductController extends Controller
{

    protected $request;
    protected $productService;

    public function __construct(Request $request, ProductService $productService)
    {
        $this->request        = $request;
        $this->productService = $productService;
    }

    public function getAll()
    {
        $data = $this->productService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getList()
    {
        $data = $this->productService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($id)
    {
        $result = $this->productService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

    public function create()
    {
        $this->validate($this->request, ProductCreateValidator::rules(), ProductCreateValidator::messages());

        $data = $this->productService->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function update()
    {
        $this->validate($this->request, ProductUpdateValidator::rules(), ProductUpdateValidator::messages());

        $data = $this->productService->update($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function delete($id)
    {
        $data = $this->productService->delete($id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
