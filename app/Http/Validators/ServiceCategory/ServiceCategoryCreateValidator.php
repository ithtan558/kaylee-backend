<?php

namespace App\Http\Validators\ServiceCategory;

use  App\Http\Validators\AbstractValidator;

class ServiceCategoryCreateValidator implements AbstractValidator
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
