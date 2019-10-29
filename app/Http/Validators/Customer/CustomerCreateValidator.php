<?php

namespace App\Http\Validators\Customer;

use  App\Http\Validators\AbstractValidator;

class CustomerCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'  => 'required',
            'phone' => 'required|unique'
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
