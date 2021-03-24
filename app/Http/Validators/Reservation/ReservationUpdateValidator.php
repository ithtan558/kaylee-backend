<?php

namespace App\Http\Validators\Reservation;

use  App\Http\Validators\AbstractValidator;

class ReservationUpdateValidator implements AbstractValidator
{
    public static function rules()
    {
        $request = app('request');
        $id      = $request['id'];
        return [
            'id'         => 'exists:reservation,id',
            'brand_id' => 'required',
            'name'  => 'required',
            'phone'      => 'required|numeric'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'id.exists'           => 'Id không tồn tại trong hệ thống.',
            'brand_id.required' => 'Chi nhánh bắt buộc.',
            'name.required' => 'Họ tên bắt buộc.',
            'phone.required'      => 'Số điện thoại bắt buộc.',
            'phone.numeric'       => 'Số điện thoại chưa đúng định dạng.'
        ];
    }
}
