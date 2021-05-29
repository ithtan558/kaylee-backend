<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VersionService;
use App\Libraries\Api;

class VersionController extends Controller
{

    protected $request;
    protected $versionService;

    public function __construct(Request $request, VersionService $versionService)
    {
        $this->request     = $request;
        $this->versionService = $versionService;
    }

    public function getDetail()
    {
        $data = $this->versionService->getDetail();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
