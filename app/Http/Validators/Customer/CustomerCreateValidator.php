<?php

namespace App\Http\Validators\Customer;

use  App\Http\Validators\AbstractValidator;

class CustomerCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'numeric|unique:customer,phone'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'first_name.required' => 'Họ bắt buộc.',
            'last_name.required'  => 'Tên bắt buộc.',
            //'phone.required'      => 'Số điện thoại bắt buộc.',
            'phone.unique'        => 'Số điện thoại đã tồn tại.',
            'phone.numeric'       => 'Số điện thoại chưa đúng định dạng.'
        ];
    }
}
