<?php

namespace App\Http\Validators\Order;

use  App\Http\Validators\AbstractValidator;

class OrderCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'cart_items'    => 'required|array',
            'cart_employee' => 'required',
            'cart_customer.last_name' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'cart_items.required'    => 'Không có dịch vụ nào trong giỏ hàng.',
            'cart_items.array'       => 'Không có dịch vụ nào trong giỏ hàng.',
            'cart_employee.required' => 'Chưa chọn nhân viên.',
            'cart_customer.last_name.required' => 'Chưa chọn khách hàng'
        ];
    }
}
