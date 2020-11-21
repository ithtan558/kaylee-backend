<?php

namespace App\Http\Validators\Order;

use  App\Http\Validators\AbstractValidator;

class SupplierOrderCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'cart_items'    => 'required|array',
            'cart_employee' => 'required',
            'supplier_id' => 'required',
            'cart_supplier_information.name' => 'required',
            'cart_supplier_information.address' => 'required',
            'cart_supplier_information.city_id' => 'required',
            'cart_supplier_information.district_id' => 'required',
            'cart_supplier_information.wards_id' => 'required',
            'cart_supplier_information.phone' => 'required',
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
            'supplier_id.required' => 'Chưa chọn nhà cung cấp',
            'cart_supplier_information.name.required' => 'Chưa nhập Họ & Tên',
            'cart_supplier_information.address.required' => 'Chưa nhập địa chỉ',
            'cart_supplier_information.city_id.required' => 'Chưa chọn thành phố',
            'cart_supplier_information.district_id.required' => 'Chưa chọn quận',
            'cart_supplier_information.wards_id.required' => 'Chưa chọn phường',
            'cart_supplier_information.phone' => 'Chưa nhập số điện thoại'
        ];
    }
}
