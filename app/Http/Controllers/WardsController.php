<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WardsService;
use App\Libraries\Api;

class WardsController extends Controller
{

    protected $request;
    protected $wardsService;

    public function __construct(Request $request, WardsService $wardsService)
    {
        $this->request      = $request;
        $this->wardsService = $wardsService;
    }

    public function getAll()
    {
        $data = $this->wardsService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getListByDistrict($district_id)
    {
        $data = $this->wardsService->getListByDistrict($district_id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
