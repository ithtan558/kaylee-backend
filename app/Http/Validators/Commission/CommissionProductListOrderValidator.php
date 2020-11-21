<?php

namespace App\Http\Validators\Commission;

use  App\Http\Validators\AbstractValidator;

class CommissionProductListOrderValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'start_date'    => 'required',
            'end_date' => 'required',
            'user_id' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'start_date.required'    => 'Ngày bắt đầu bắt buộc.',
            'end_date.required'       => 'Ngày kết thúc bắt buộc.',
            'user_id.required' => 'Nhân viên bắt buộc.'
        ];
    }
}
