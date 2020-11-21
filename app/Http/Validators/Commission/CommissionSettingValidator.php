<?php

namespace App\Http\Validators\Commission;

use  App\Http\Validators\AbstractValidator;

class CommissionSettingValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'user_id' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'user_id.required' => 'Nhân viên bắt buộc.'
        ];
    }
}
