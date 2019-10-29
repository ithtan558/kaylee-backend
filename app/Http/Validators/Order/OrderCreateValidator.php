<?php

namespace App\Http\Validators\Order;

use  App\Http\Validators\AbstractValidator;

class OrderCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'cart_items' => 'required|array'
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
