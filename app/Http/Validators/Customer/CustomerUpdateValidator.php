<?php

namespace App\Http\Validators\Customer;

use  App\Http\Validators\AbstractValidator;

class CustomerUpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        $request = app('request');
        $id      = $request['id'];
        return [
            'id'    => 'exists:customer,id',
            'name'  => 'required',
            'phone' => 'required|unique:customer,phone,' . $id
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
            'phone.unique' => 'Số điện thoại này đã có người sử dụng.'
        ];
    }
}
