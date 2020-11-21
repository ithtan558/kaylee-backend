<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class UpdatePasswordValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'user_id'              => 'required',
            'password'             => 'required',
            'token_reset_password' => 'required',
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'user_id.required'              => 'Tài khoản bắt buộc',
            'password.required'             => 'Mật khẩu bắt buộc',
            'token_reset_password.required' => 'Chứng thực bắt buộc'
        ];
    }
}
