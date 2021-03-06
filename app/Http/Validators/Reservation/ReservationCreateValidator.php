<?php

namespace App\Http\Validators\Reservation;

use  App\Http\Validators\AbstractValidator;

class ReservationCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'brand_id' => 'required',
            'name'  => 'required',
            'phone'      => 'required|numeric',
            'datetime'      => 'required|date'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'brand_id.required' => 'Chi nhánh bắt buộc.',
            'name.required' => 'Họ tên bắt buộc.',
            'phone.required'      => 'Số điện thoại bắt buộc.',
            'datetime.required'       => 'Ngày hẹn bắt buộc.'
        ];
    }
}
