<?php

namespace App\Http\Validators\Service;

use  App\Http\Validators\AbstractValidator;

class ServiceCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
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
        return [];
    }
}
