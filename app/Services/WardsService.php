<?php

namespace App\Services;

use App\Repositories\WardsRepository;

class WardsService extends BaseService
{
    protected $wardsRep;

    public function __construct(WardsRepository $wardsRep)
    {
        $this->wardsRep = $wardsRep;
    }

    public function getAll()
    {
        $data = $this->wardsRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getListByDistrict($district_id)
    {
        $data = $this->wardsRep->getListByDistrict($district_id);
        $this->setData($data);

        return $this->getResponseData();
    }

}
