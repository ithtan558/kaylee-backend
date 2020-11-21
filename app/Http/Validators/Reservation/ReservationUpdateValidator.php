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
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required|numeric|unique:reservation,phone,'.$id
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
            'first_name.required' => 'Họ bắt buộc.',
            'last_name.required'  => 'Tên bắt buộc.',
            'phone.required'      => 'Số điện thoại bắt buộc.',
            'phone.unique'        => 'Số điện thoại đã tồn tại.',
            'phone.numeric'       => 'Số điện thoại chưa đúng định dạng.'
        ];
    }
}
