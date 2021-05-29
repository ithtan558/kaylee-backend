<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Libraries\Api;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\Topics;

class NotificationController extends Controller
{

    protected $request;
    protected $notificationService;

    public function __construct(Request $request, NotificationService $notificationService)
    {
        $this->request             = $request;
        $this->notificationService = $notificationService;
    }

    public function getList()
    {
        $data = $this->notificationService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getDetail($id)
    {
        $result = $this->notificationService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY], $this->request);
    }

    public function delete($id)
    {
        $data = $this->notificationService->delete($id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function updateStatus()
    {
        $data = $this->notificationService->updateStatus($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function deleteAll()
    {
        $data = $this->notificationService->deleteAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

    public function getCount()
    {
        $data = $this->notificationService->getCount();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY], $this->request);
    }

}
