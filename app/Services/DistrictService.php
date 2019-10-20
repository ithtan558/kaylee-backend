<?php

namespace App\Services;

use App\Repositories\DistrictRepository;

class DistrictService extends BaseService
{
    protected $districtRep;

    public function __construct(DistrictRepository $districtRep)
    {
        $this->districtRep = $districtRep;
    }

    public function getAll()
    {
        $data = $this->districtRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getListByCity($city_id)
    {
        $data = $this->districtRep->getListByCity($city_id);
        $this->setData($data);

        return $this->getResponseData();
    }

}
