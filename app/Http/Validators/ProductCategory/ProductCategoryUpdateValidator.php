<?php

namespace App\Http\Validators\ProductCategory;

use  App\Http\Validators\AbstractValidator;

class ProductCategoryUpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'id'   => 'exists:product_category,id',
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
