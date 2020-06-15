<?php

namespace App\Services;

use App\Repositories\OtpRepository;
use Illuminate\Http\Request;

class OtpService extends BaseService
{
    protected $otpRep;

    public function __construct(OtpRepository $otpRep)
    {
        $this->otpRep = $otpRep;
    }

}
