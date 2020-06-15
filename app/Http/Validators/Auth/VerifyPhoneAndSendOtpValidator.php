<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class VerifyPhoneAndSendOtpValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'phone'  => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'phone.required'  => 'Số điện thoại bắt buộc'
        ];
    }
}
