<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class UpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'email'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'first_name.required' => 'Tên bắt buộc.',
            'last_name.required'  => 'Họ bắt buộc.',
            'email.email'         => 'Email không đúng định dạng.'
        ];
    }
}
