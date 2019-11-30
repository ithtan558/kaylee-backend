<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class LoginValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'account'  => 'required',
            'password' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'account.required'  => 'Tài khoản không chính xác1.',
            'password.required' => 'Tài khoản không chính xác2.'
        ];
    }
}
