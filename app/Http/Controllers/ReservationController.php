<?php

namespace App\Http\Controllers;

use App\Http\Validators\Reservation\ReservationCreateValidator;
use App\Http\Validators\Reservation\ReservationUpdateStatusValidator;
use App\Http\Validators\Reservation\ReservationUpdateValidator;
use Illuminate\Http\Request;
use App\Services\ReservationService;
use App\Libraries\Api;

class ReservationController extends Controller
{

    protected $request;
    protected $reservationService;

    public function __construct(Request $request, ReservationService $reservationService)
    {
        $this->request         = $request;
        $this->reservationService = $reservationService;
    }

    public function getAll()
    {
        $data = $this->reservationService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getCount()
    {
        $data = $this->reservationService->getCount();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getList()
    {
        $data = $this->reservationService->getList($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getByPhoneOrName()
    {
        $data = $this->reservationService->getByPhoneOrName($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($id)
    {
        $result = $this->reservationService->getDetail($id);

        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

    public function create()
    {
        $this->validate($this->request, ReservationCreateValidator::rules(), ReservationCreateValidator::messages());

        $data = $this->reservationService->create($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function update()
    {
        $this->validate($this->request, ReservationUpdateValidator::rules(), ReservationUpdateValidator::messages());

        $data = $this->reservationService->update($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function updateStatus()
    {
        $this->validate($this->request, ReservationUpdateStatusValidator::rules(), ReservationUpdateStatusValidator::messages());

        $data = $this->reservationService->updateStatus($this->request);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function delete($id)
    {
        $data = $this->reservationService->delete($id);

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

}
