<?php

namespace App\Http\Validators\Customer;

use  App\Http\Validators\AbstractValidator;

class CustomerCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'  => 'required',
            'phone' => 'required|unique:customer,phone',
            'email' => 'unique:customer,email'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required' => 'Tên bắt buộc.',
            'phone.required' => 'Số điện thoại bắt buộc.',
            'phone.unique' => 'Số điện thoại này đã có người sử dụng.',
            'email.unique' => 'Email này đã có người sử dụng.'
        ];
    }
}
