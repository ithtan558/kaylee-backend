<?php

namespace App\Http\Controllers;

use App\Http\Validators\Report\ReportValidator;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Libraries\Api;

class ReportController extends Controller
{

    protected $request;
    protected $reportService;

    public function __construct(Request $request, ReportService $reportService)
    {
        $this->request       = $request;
        $this->reportService = $reportService;
    }

    public function getTotal()
    {
        $this->validate($this->request, ReportValidator::rules(), ReportValidator::messages());
        $data = $this->reportService->getTotal($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getTotalByEmployeeAndDate()
    {
        $this->validate($this->request, ReportValidator::rules(), ReportValidator::messages());
        $data = $this->reportService->getTotalByEmployeeAndDate($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getTotalByServiceAndDate()
    {
        $this->validate($this->request, ReportValidator::rules(), ReportValidator::messages());
        $data = $this->reportService->getTotalByServiceAndDate($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getTotalByProductAndDate()
    {
        $this->validate($this->request, ReportValidator::rules(), ReportValidator::messages());
        $data = $this->reportService->getTotalByProductAndDate($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
