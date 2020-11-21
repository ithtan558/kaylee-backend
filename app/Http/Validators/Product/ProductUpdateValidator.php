<?php

namespace App\Http\Validators\Product;

use  App\Http\Validators\AbstractValidator;

class ProductUpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'id'         => 'exists:product,id',
            'code'      => 'required',
            'name'      => 'required',
            'brand_ids' => 'required',
            /*'category_id' => 'required|integer',*/
            'price'     => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'id.exists'           => 'Id không tồn tại trong hệ thống.',
            'code.required'      => 'Mã bắt buộc.',
            'name.required'      => 'Tên bắt buộc.',
            'brand_ids.required' => 'Cửa hàng bắt buộc.',
            /*'category_id.required' => 'Loại dịch vụ bắt buộc.',*/
            'price.required'     => 'Giá bắt buộc.'
        ];
    }
}
