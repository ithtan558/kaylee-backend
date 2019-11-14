<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;

class RegisterValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'name'            => 'required',
            'name_client'     => 'required',
            'location_client' => 'required',
            'phone_client'    => 'required|numeric',
            'city_id'         => 'required|numeric',
            'district_id'     => 'required|numeric',
            'phone'           => 'required|numeric|unique:user,phone',
            'password'        => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'name.required'            => 'Họ tên bắt buộc.',
            'name_client.required'     => 'Tên cửa hàng bắt buộc.',
            'location_client.required' => 'Địa chỉ cửa hàng bắt buộc.',
            'phone_client.required'    => 'Số điện thoại cửa hàng bắt buộc.',
            'city_id.required'         => 'Thành phố bắt buộc.',
            'city_id.numeric'          => 'Thành phố bắt buộc.',
            'district_id.required'     => 'Quận/Huyện bắt buộc.',
            'district_id.numeric'      => 'Quận/Huyện bắt buộc.',
            'phone.required'           => 'Số điện thoại bắt buộc.',
            'phone.unique'             => 'Số điện thoại đã tồn tại.',
            'phone.numeric'            => 'Số điện thoại chưa đúng định dạng.',
            'password.required'        => 'Mật khẩu bắt buộc.'
        ];
    }
}
