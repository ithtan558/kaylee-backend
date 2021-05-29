<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CityService;
use App\Libraries\Api;

class CityController extends Controller
{

    protected $request;
    protected $cityService;

    public function __construct(Request $request, CityService $cityService)
    {
        $this->request     = $request;
        $this->cityService = $cityService;
    }

    public function getAll()
    {
        $data = $this->cityService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
