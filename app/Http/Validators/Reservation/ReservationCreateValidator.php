<?php

namespace App\Http\Validators\Reservation;

use  App\Http\Validators\AbstractValidator;

class ReservationCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'brand_id' => 'required',
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|numeric|unique:customer,phone'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'brand_id.required' => 'Chi nhánh bắt buộc.',
            'first_name.required' => 'Họ bắt buộc.',
            'last_name.required'  => 'Tên bắt buộc.',
            'phone.required'      => 'Số điện thoại bắt buộc.',
            'phone.unique'        => 'Số điện thoại đã tồn tại.',
            'phone.numeric'       => 'Số điện thoại chưa đúng định dạng.'
        ];
    }
}
