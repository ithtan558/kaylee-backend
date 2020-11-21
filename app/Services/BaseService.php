<?php

namespace App\Services;

use App\Repositories\BrandRepository;
use App\Repositories\CityRepository;
use App\Repositories\ClientRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\WardsRepository;

use Illuminate\Http\Response;
use App\Helpers\CommonHelper;
use stdClass;

class BaseService
{
    protected $data = null;
    protected $message = null;
    protected $statusCode = Response::HTTP_OK;
    protected $cityRep;
    protected $districtRep;
    protected $wardsRep;

    public function __construct(
        CityRepository $cityRep,
        DistrictRepository $districtRep,
        WardsRepository $wardsRep
    )
    {
        $this->cityRep     = $cityRep;
        $this->districtRep = $districtRep;
        $this->wardsRep    = $wardsRep;
    }

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

    protected function setMessage($mgs = null)
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

    protected function setMessageDataStatusCode($mgs = null, $data = [], $status_code = Response::HTTP_OK)
    {
        if ($status_code == Response::HTTP_OK) {
            $this->message = $mgs;
        } else {
            $this->message = 'Lỗi';
        }
        $this->data       = $data;
        $this->statusCode = $status_code;
    }

    public function setCityDistrictWards(&$data)
    {
        if (!empty($data->city_id)) {
            $cityRep = app(CityRepository::class);
            $city    = $cityRep->find($data->city_id);
            if (!empty($city)) {
                $obj        = new stdClass();
                $obj->id    = $city->id;
                $obj->name  = $city->name;
                $data->city = $obj;
                unset($data->city_id);
            }
        }
        if (!empty($data->district_id)) {
            $districtRep = app(DistrictRepository::class);
            $district    = $districtRep->find($data->district_id);
            if (!empty($district)) {
                $obj            = new stdClass();
                $obj->id        = $district->id;
                $obj->name      = $district->name;
                $data->district = $obj;
                unset($data->district_id);
            }
        }
        if (!empty($data->wards_id)) {
            $wardsRep = app(WardsRepository::class);
            $wards    = $wardsRep->find($data->wards_id);
            if (!empty($wards)) {
                $obj            = new stdClass();
                $obj->id        = $wards->id;
                $obj->name      = $wards->name;
                $data->wards = $obj;
                unset($data->wards_id);
            }
        }

        if (!empty($data->hometown_city_id)) {
            $cityRep = app(CityRepository::class);
            $city    = $cityRep->find($data->hometown_city_id);
            if (!empty($city)) {
                $obj                 = new stdClass();
                $obj->id             = $city->id;
                $obj->name           = $city->name;
                $data->hometown_city = $obj;
                unset($data->hometown_city_id);
            }
        }
        return $data;
    }
}
