<?php

namespace App\Http\Validators\Auth;

use  App\Http\Validators\AbstractValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegisterValidator implements AbstractValidator
{
    public static function rules()
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => ['required', 'numeric', Rule::unique('user')->where('company_id', $this->phone)],
            'password'   => 'required',
            'email'      => 'email'
        ];
    }

    /**
     * @return array
     */
    public static function messages()
    {
        return [
            'first_name.required' => 'Tên bắt buộc.',
            'last_name.required'  => 'Họ bắt buộc.',
            'phone.required'      => 'Số điện thoại đăng ký bắt buộc.',
            'phone.unique'        => 'Số điện thoại đăng ký đã tồn tại.',
            'phone.numeric'       => 'Số điện thoại chưa đúng định dạng.',
            'password.required'   => 'Mật khẩu bắt buộc.',
            'email.email'         => 'Email không đúng định dạng.'
        ];
    }
}
