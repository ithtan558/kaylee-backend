<?php

namespace App\Http\Validators\Employee;

use  App\Http\Validators\AbstractValidator;

class EmployeeCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|numeric|unique:user,phone',
            'role_id'    => 'required',
            'brand_id'   => 'required'
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
            'phone.required'      => 'Số điện thoại bắt buộc.',
            'phone.unique'        => 'Số điện thoại đã tồn tại.',
            'phone.numeric'       => 'Số điện thoại chưa đúng định dạng.',
            'role_id.required'    => 'Loại tài khoản bắt buộc.',
            'brand_id.required'   => 'Chi nhánh bắt buộc.'
        ];
    }
}
