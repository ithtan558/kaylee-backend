<?php

namespace App\Http\Validators\Commission;

use  App\Http\Validators\AbstractValidator;

class CommissionUpdateSettingValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'commission_product' => 'required',
            'commission_service' => 'required',
            'user_id'            => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'commission_product.required' => 'Hoa hồng sản phẩm bắt buộc.',
            'commission_service.required' => 'Hoa hồng dịch vụ bắt buộc.',
            'user_id.required'            => 'Nhân viên bắt buộc.'
        ];
    }
}
