<?php

namespace App\Http\Validators\Brand;

use  App\Http\Validators\AbstractValidator;

class BrandCreateValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'        => 'required',
            'phone'       => 'required',
            'city_id'     => 'required',
            'district_id' => 'required',
            'start_time'  => 'required',
            'end_time'    => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required' => 'Tên bắt buộc.',
            'phone.required' => 'Số điện thoại bắt buộc.',
            'city_id.required' => 'Thành phố bắt buộc.',
            'district_id.required' => 'Quận bắt buộc.',
            'start_time.required' => 'Giờ mở cửa bắt buộc.',
            'end_time.required' => 'Giờ đóng cửa bắt buộc.',
        ];
    }
}
