<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class VerifyOtpValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'otp'  => 'required',
            'user_id'  => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'otp.required'  => 'Otp bắt buộc',
            'user_id.required'  => 'Tài khoản bắt buộc'
        ];
    }
}
