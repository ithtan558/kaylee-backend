<?php

namespace App\Services;

use Illuminate\Http\Response;
use App\Helpers\CommonHelper;

class BaseService
{
    protected $data = '';
    protected $message = '';
    protected $statusCode = Response::HTTP_OK;

    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    protected function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    protected function getResponseData()
    {
        return [
            RESPONSE_KEY => [
                DATA_KEY    => $this->data,
                MESSAGE_KEY => $this->message,
            ],
            STT_CODE_KEY => $this->getStatusCode()
        ];
    }

    protected function setMessage($mgs = '')
    {
        $this->message = $mgs;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getCurrentUser($key = '')
    {
        return CommonHelper::getAuth($key);
    }
}
