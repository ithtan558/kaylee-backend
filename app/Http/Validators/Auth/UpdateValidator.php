<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class UpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'  => 'required',
            'email'      => 'email'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required'  => 'Họ tên bắt buộc.',
            'email.email'         => 'Email không đúng định dạng.'
        ];
    }
}
