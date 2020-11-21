<?php

namespace App\Http\Validators\Reservation;

use  App\Http\Validators\AbstractValidator;

class ReservationUpdateStatusValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'id'      => 'required|numeric'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'id.required' => 'Lịch hẹn bắt buộc.',
            'id.numeric'       => 'Lịch hẹn chưa đúng định dạng.'
        ];
    }
}
