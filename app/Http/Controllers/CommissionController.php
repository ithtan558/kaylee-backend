<?php

namespace App\Http\Controllers;

use App\Http\Validators\Commission\CommissionDetailValidator;
use App\Http\Validators\Commission\CommissionProductListOrderValidator;
use App\Http\Validators\Commission\CommissionServiceListOrderValidator;
use App\Http\Validators\Commission\CommissionSettingValidator;
use App\Http\Validators\Commission\CommissionUpdateSettingValidator;
use Illuminate\Http\Request;
use App\Services\CommissionService;
use App\Libraries\Api;

class CommissionController extends Controller
{

    protected $request;
    protected $commissionService;

    public function __construct(Request $request, CommissionService $commissionService)
    {
        $this->request       = $request;
        $this->commissionService = $commissionService;
    }

    public function detail()
    {
        $this->validate($this->request, CommissionDetailValidator::rules(), CommissionDetailValidator::messages());
        $data = $this->commissionService->detail($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getListProduct()
    {
        $this->validate($this->request, CommissionProductListOrderValidator::rules(), CommissionProductListOrderValidator::messages());
        $data = $this->commissionService->getList($this->request, true);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getListService()
    {
        $this->validate($this->request, CommissionProductListOrderValidator::rules(), CommissionProductListOrderValidator::messages());
        $data = $this->commissionService->getList($this->request, false, true);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getDetailSetting()
    {
        $this->validate($this->request, CommissionSettingValidator::rules(), CommissionSettingValidator::messages());
        $data = $this->commissionService->getDetailSetting($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function updateSetting()
    {
        $this->validate($this->request, CommissionUpdateSettingValidator::rules(), CommissionUpdateSettingValidator::messages());
        $data = $this->commissionService->updateSetting($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
