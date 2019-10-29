<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DistrictService;
use App\Libraries\Api;

class DistrictController extends Controller
{

    protected $request;
    protected $districtService;

    public function __construct(Request $request, DistrictService $districtService)
    {
        $this->request         = $request;
        $this->districtService = $districtService;
    }

    public function getAll()
    {
        $data = $this->districtService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getListByCity($city_id)
    {
        $data = $this->districtService->getListByCity($city_id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
