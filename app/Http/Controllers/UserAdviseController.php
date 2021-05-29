<?php

namespace App\Http\Controllers;

use App\Http\Validators\UserAdvise\UserAdviseCreateValidator;
use App\Http\Validators\UserAdvise\UserAdviseUpdateValidator;
use Illuminate\Http\Request;
use App\Services\UserAdviseService;
use App\Libraries\Api;

class UserAdviseController extends Controller
{

    protected $request;
    protected $userAdviseService;

    public function __construct(Request $request, UserAdviseService $userAdviseService)
    {
        $this->request      = $request;
        $this->userAdviseService = $userAdviseService;
    }

    public function create()
    {
        $this->validate($this->request, UserAdviseCreateValidator::rules(), UserAdviseCreateValidator::messages());

        $data = $this->userAdviseService->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
