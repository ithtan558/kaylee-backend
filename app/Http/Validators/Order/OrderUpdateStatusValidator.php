<?php

namespace App\Http\Validators\Order;

use  App\Http\Validators\AbstractValidator;

class OrderUpdateStatusValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'id'         => 'exists:order,id'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'id.exists'           => 'Id không tồn tại trong hệ thống.'
        ];
    }
}
