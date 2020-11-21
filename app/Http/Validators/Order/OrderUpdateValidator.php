<?php

namespace App\Http\Validators\Order;

use  App\Http\Validators\AbstractValidator;

class OrderUpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'id'         => 'exists:order,id',
            'cart_items'    => 'required|array',
            'cart_employee' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'id.exists'           => 'Id không tồn tại trong hệ thống.',
            'cart_items.required'    => 'Không có dịch vụ nào trong giỏ hàng.',
            'cart_items.array'       => 'Không có dịch vụ nào trong giỏ hàng.',
            'cart_employee.required' => 'Chưa chọn nhân viên.'
        ];
    }
}
