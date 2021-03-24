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
            'id'         => 'exists:customer,id',
            'name'  => 'required',
            'phone'      => 'numeric|unique:customer,phone,' . $id
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'id.exists'           => 'Id không tồn tại trong hệ thống.',
            'name.required'  => 'Họ tên bắt buộc.',
            //'phone.required'      => 'Số điện thoại bắt buộc.',
            'phone.unique'        => 'Số điện thoại đã tồn tại.',
            'phone.numeric'       => 'Số điện thoại chưa đúng định dạng.'
        ];
    }
}
