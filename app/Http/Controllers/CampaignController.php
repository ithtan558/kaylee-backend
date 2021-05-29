<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CampaignService;
use App\Libraries\Api;

class CampaignController extends Controller
{

    protected $request;
    protected $campaignService;

    public function __construct(Request $request, CampaignService $campaignService)
    {
        $this->request      = $request;
        $this->campaignService = $campaignService;
    }

    public function getAll()
    {
        $data = $this->campaignService->getAll();
        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
