<?php

namespace App\Http\Validators\UserAdvise;

use  App\Http\Validators\AbstractValidator;

class UserAdviseCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'        => 'required',
            'phone'       => 'required',
            'email'     => 'required|email',
            'content' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required'        => 'Tên bắt buộc.',
            'phone.required'       => 'Số điện thoại bắt buộc.',
            'email.required'     => 'Email bắt buộc.',
            'email.email'     => 'Email sai định dạng.',
            'content.required' => 'Nội dung bắt buộc.'
        ];
    }
}
