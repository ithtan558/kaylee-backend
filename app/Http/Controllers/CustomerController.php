<?php

namespace App\Http\Controllers;

use App\Http\Validators\Customer\CustomerCreateValidator;
use App\Http\Validators\Customer\CustomerUpdateValidator;
use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Libraries\Api;

class CustomerController extends Controller
{

    protected $request;
    protected $customerService;

    public function __construct(Request $request, CustomerService $customerService)
    {
        $this->request         = $request;
        $this->customerService = $customerService;
    }

    public function getAll()
    {
        $data = $this->customerService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getList()
    {
        $data = $this->customerService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getByPhoneAndName()
    {
        $data = $this->customerService->getByPhone($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($id)
    {
        $result = $this->customerService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

    public function create()
    {
        $this->validate($this->request, CustomerCreateValidator::rules(), CustomerCreateValidator::messages());

        $data = $this->customerService->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function update()
    {
        $this->validate($this->request, CustomerUpdateValidator::rules(), CustomerUpdateValidator::messages());

        $data = $this->customerService->update($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function delete($id)
    {
        $data = $this->customerService->delete($id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
