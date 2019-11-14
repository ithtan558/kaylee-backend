<?php

namespace App\Http\Validators\Employee;

use  App\Http\Validators\AbstractValidator;

class EmployeeCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'     => 'required',
            'phone'    => 'required|numeric|unique:user,phone',
            'password' => 'required',
            'role_id'  => 'required',
            'brand_id' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required'     => 'Họ tên bắt buộc.',
            'phone.required'    => 'Số điện thoại bắt buộc.',
            'phone.unique'      => 'Số điện thoại đã tồn tại.',
            'phone.numeric'     => 'Số điện thoại chưa đúng định dạng.',
            'password.required' => 'Mật khẩu bắt buộc.',
            'role_id.required'  => 'Loại tài khoản bắt buộc.',
            'brand_id.required' => 'Chi nhánh bắt buộc.'
        ];
    }
}
