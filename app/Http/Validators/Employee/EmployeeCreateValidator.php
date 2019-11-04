<?php

namespace App\Http\Validators\Brand;

use  App\Http\Validators\AbstractValidator;

class EmployeeCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'     => 'required',
            'phone'    => 'required|numeric|unique:user,phone',
            'location' => 'required',
            'brand_id' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required'     => 'Tên bắt buộc.',
            'phone.required'    => 'Số điện thoại bắt buộc.',
            'phone.unique'      => 'Số điện thoại đã tồn tại.',
            'location.required' => 'Địa chỉ bắt buộc.',
            'brand_id.required' => 'Thành phố bắt buộc.'
        ];
    }
}
