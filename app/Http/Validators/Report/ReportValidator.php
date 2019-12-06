<?php

namespace App\Http\Validators\Report;

use  App\Http\Validators\AbstractValidator;

class ReportValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            /*'start_date' => 'required',
            'end_date' => 'required'*/
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            /*'start_date.required' => 'Chưa chọn ngày.',
            'end_date.required' => 'Chưa chọn ngày.'*/
        ];
    }
}
