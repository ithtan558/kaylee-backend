<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Validators\Auth\LoginValidator;
use App\Libraries\Api;

class AuthController extends Controller
{
    protected $request;
    protected $authService;

    public function __construct(Request $request, AuthService $authServiceInstance)
    {
        $this->request = $request;
        $this->authService = $authServiceInstance;
    }

    public function login()
    {
        $this->validate($this->request, LoginValidator::rules(), LoginValidator::messages());
        $data = $this->authService->login($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function logout()
    {
        $data = $this->authService->logout();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getUserInfo()
    {
        $data = $this->authService->getAuthenticatedUser();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }


    public function create()
    {
        $result = $this->authService->create();
        return Api::response($result);
    }
}
