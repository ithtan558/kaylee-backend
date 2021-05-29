<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Libraries\Curl;
use App\Models\LogJob;

class SendOtpService extends BaseService
{

    public function __construct(
    )
    {
    }

    public function sendOtp($otp, $phone)
    {
        // Send otp
        $dataOtp = [
            'u'     => USERNAME,
            'pwd'   => PASSWORD,
            'from'  => FROM,
            'phone' => $phone,
            'sms'   => 'KAYLEE-Ma OTP cua ban la ' . $otp . '. Vui long nhap ma de tiep tuc',
            'bid'   => CommonHelper::createRandomCode(),
            'type'  => TYPE,
            'json'  => JSON,
        ];
        Curl::callApi(LINK_OTP, $dataOtp);

    }

}
