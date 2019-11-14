<?php

namespace App\Http\Validators\Employee;

use  App\Http\Validators\AbstractValidator;

class EmployeeUpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        $request = app('request');
        $id      = $request['id'];
        return [
            'name'            => 'required',
            'phone'           => 'required|numeric|unique:user,phone,' . $id,
            'password'        => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required'            => 'Họ tên bắt buộc.',
            'phone.required'           => 'Số điện thoại bắt buộc.',
            'phone.unique'             => 'Số điện thoại đã tồn tại.',
            'phone.numeric'            => 'Số điện thoại chưa đúng định dạng.',
            'password.required'        => 'Mật khẩu bắt buộc.'
        ];
    }
}
