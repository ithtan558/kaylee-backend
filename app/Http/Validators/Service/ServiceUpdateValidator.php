<?php

namespace App\Http\Validators\Service;

use  App\Http\Validators\AbstractValidator;

class ServiceUpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'id'          => 'exists:service,id',
            'name'        => 'required',
            'brand_ids'   => 'required',
            'category_id' => 'required|integer',
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
            'name.required' => 'Tên bắt buộc.',
            'brand_ids.required' => 'Của hàng bắt buộc.',
            'category_id.required' => 'Loại dịch vụ bắt buộc.',
            'time.required' => 'Thời gian làm dịch vụ bắt buộc.',
            'price.required' => 'Giá bắt buộc.'
        ];
    }
}
