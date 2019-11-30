<?php

namespace App\Http\Controllers;

use App\Http\Validators\Service\ServiceCreateValidator;
use App\Http\Validators\Service\ServiceUpdateValidator;
use Illuminate\Http\Request;
use App\Services\ServiceService;
use App\Libraries\Api;

class ServiceController extends Controller
{

    protected $request;
    protected $serviceService;

    public function __construct(Request $request, ServiceService $serviceService)
    {
        $this->request        = $request;
        $this->serviceService = $serviceService;
    }

    public function getAll()
    {
        $data = $this->serviceService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getList()
    {
        $data = $this->serviceService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($id)
    {
        $result = $this->serviceService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

    public function create()
    {
        $this->validate($this->request, ServiceCreateValidator::rules(), ServiceCreateValidator::messages());

        $data = $this->serviceService->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function update()
    {
        $this->validate($this->request, ServiceUpdateValidator::rules(), ServiceUpdateValidator::messages());

        $data = $this->serviceService->update($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function delete($id)
    {
        $data = $this->serviceService->delete($id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
