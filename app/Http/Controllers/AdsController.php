<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdsService;
use App\Libraries\Api;

class AdsController extends Controller
{

    protected $request;
    protected $adsService;

    public function __construct(Request $request, AdsService $adsService)
    {
        $this->request     = $request;
        $this->adsService = $adsService;
    }

    public function getAll()
    {
        $data = $this->adsService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
