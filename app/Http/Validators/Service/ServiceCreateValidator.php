<?php

namespace App\Http\Validators\Service;

use  App\Http\Validators\AbstractValidator;

class ServiceCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'code'      => 'required',
            'name'      => 'required',
            'brand_ids' => 'required',
            'time'      => 'required',
            'price'     => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'code.required'      => 'Mã bắt buộc.',
            'name.required'      => 'Tên bắt buộc.',
            'brand_ids.required' => 'Của hàng bắt buộc.',
            /*'category_id.integer' => 'Loại dịch vụ bắt buộc.',*/
            'time.required'      => 'Thời gian làm dịch vụ bắt buộc.',
            'price.required'     => 'Giá bắt buộc.'
        ];
    }
}
