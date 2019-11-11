<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class LoginValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'account'  => 'required|min:3',
            'password' => 'required|min:3'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'account.required'  => 'Xin vui lòng điền đầy đủ thông tin',
            'password.required' => 'Xin vui lòng điền đầy đủ thông tin'
        ];
    }
}
