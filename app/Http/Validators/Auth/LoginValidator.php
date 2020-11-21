<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class LoginValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'account'  => 'required',
            'password' => 'required',
            'token' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'account.required'  => 'Số điện thoại bắt buộc',
            'password.required' => 'Mật khẩu bắt buộc',
            'token.required' => 'Token bắt buộc'
        ];
    }
}
