<?php

namespace App\Http\Validators\ProductCategory;

use  App\Http\Validators\AbstractValidator;

class ProductCategoryCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required' => 'Tên bắt buộc.'
        ];
    }
}
