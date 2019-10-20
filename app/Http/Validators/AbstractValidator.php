<?php

/**
 * Created by PhpStorm.
 * User: An Huynh
 * Date: 2018/08/28
 * Time: 3:44 PM
 */

namespace App\Http\Validators;

interface AbstractValidator
{

    /**
     * @return array
     */
    public static function rules();

    /**
     * @return array
     */
    public static function messages();
}
