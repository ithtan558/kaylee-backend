<?php

namespace App\Http\Controllers;

use App\Http\Validators\Brand\BrandCreateValidator;
use App\Http\Validators\Brand\BrandUpdateValidator;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Libraries\Api;

class BrandController extends Controller
{

    protected $request;
    protected $brandService;

    public function __construct(Request $request, BrandService $brandService)
    {
        $this->request      = $request;
        $this->brandService = $brandService;
    }

    public function getAll()
    {
        $data = $this->brandService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getList()
    {
        $data = $this->brandService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($id)
    {
        $result = $this->brandService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

    public function create()
    {
        $this->validate($this->request, BrandCreateValidator::rules(), BrandCreateValidator::messages());

        $data = $this->brandService->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function update()
    {
        $this->validate($this->request, BrandUpdateValidator::rules(), BrandUpdateValidator::messages());

        $data = $this->brandService->update($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function delete($id)
    {
        $data = $this->brandService->delete($id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
