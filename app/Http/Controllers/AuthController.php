<?php

namespace App\Http\Controllers;

use App\Http\Validators\Auth\RegisterValidator;
use App\Http\Validators\Auth\UpdatePasswordValidator;
use App\Http\Validators\Auth\UpdateValidator;
use App\Http\Validators\Auth\VerifyOtpValidator;
use App\Http\Validators\Auth\VerifyPhoneAndSendOtpValidator;
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
        $this->request     = $request;
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

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function verifyPhoneAndSendOtp()
    {
        $this->validate($this->request, VerifyPhoneAndSendOtpValidator::rules(), VerifyPhoneAndSendOtpValidator::messages());
        $data = $this->authService->verifyPhoneAndSendOtp($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function verifyOtp()
    {
        $this->validate($this->request, VerifyOtpValidator::rules(), VerifyOtpValidator::messages());
        $data = $this->authService->verifyOtp($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function verifyOtpForRegister()
    {
        $this->validate($this->request, VerifyOtpValidator::rules(), VerifyOtpValidator::messages());
        $data = $this->authService->verifyOtpForRegister($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function updatePassword()
    {
        $this->validate($this->request, UpdatePasswordValidator::rules(), UpdatePasswordValidator::messages());
        $data = $this->authService->updatePassword($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getUserInfo()
    {
        $data = $this->authService->getAuthenticatedUser();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function register()
    {
        $this->validate($this->request, RegisterValidator::rules(), RegisterValidator::messages());
        $data = $this->authService->create($this->request);
        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function update()
    {
        $this->validate($this->request, UpdateValidator::rules(), UpdateValidator::messages());
        $data = $this->authService->update($this->request);
        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function checkExpired()
    {
        $data = $this->authService->checkExpired($this->request);
        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function clickWarning()
    {
        $data = $this->authService->clickWarning($this->request);
        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }
}
