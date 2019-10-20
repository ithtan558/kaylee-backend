<?php

namespace App\Http\Validators\Brand;

use  App\Http\Validators\AbstractValidator;

class BrandCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'          => 'required',
            'location'    => 'required',
            'city_id'  => 'required',
            'district_id'      => 'required',
            'start_time'     => 'required',
            'end_time'         => 'required'
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
