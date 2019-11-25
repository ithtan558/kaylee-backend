<?php

namespace App\Http\Validators\ServiceCategory;

use  App\Http\Validators\AbstractValidator;

class ServiceCategoryUpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'id'          => 'exists:service_category,id',
            'name'        => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required'        => 'Tên bắt buộc.'
        ];
    }
}
