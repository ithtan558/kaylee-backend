<?php

namespace App\Http\Validators\Service;

use  App\Http\Validators\AbstractValidator;

class ServiceCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'        => 'required',
            'code'        => 'required',
            'brand_ids'   => 'required',
            /*'category_id' => 'required|integer',*/
            'time'        => 'required',
            'price'       => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required'       => 'Tên bắt buộc.',
            'code.required'       => 'Mã bắt buộc.',
            'brand_ids.required'  => 'Của hàng bắt buộc.',
            /*'category_id.integer' => 'Loại dịch vụ bắt buộc.',*/
            'time.required'       => 'Thời gian làm dịch vụ bắt buộc.',
            'price.required'      => 'Giá bắt buộc.'
        ];
    }
}
