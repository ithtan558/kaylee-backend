<?php

namespace App\Jobs;

use App\Services\SendOtpService;

class SendOtp extends Job
{

    protected $otp;
    protected $phone;
    protected $sendOtpService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($otp, $phone)
    {
        $this->otp = $otp;
        $this->phone = $phone;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        SendOtpService $sendOtpService
    )
    {
        $this->sendOtpService = $sendOtpService;
        $this->sendOtpService->sendOtp($this->otp, $this->phone);
    }
}
