<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Libraries\Api;

class OrderReasonCancelController extends Controller
{

    protected $request;
    protected $roleService;

    public function __construct(Request $request, RoleService $roleService)
    {
        $this->request     = $request;
        $this->roleService = $roleService;
    }

    public function getAll()
    {
        $data = $this->roleService->getAll($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
