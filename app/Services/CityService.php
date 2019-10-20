<?php

namespace App\Services;

use App\Repositories\CityRepository;
use Illuminate\Http\Request;

class CityService extends BaseService
{
    protected $cityRep;

    public function __construct(CityRepository $cityRep)
    {
        $this->cityRep = $cityRep;
    }

    public function getAll()
    {
        $data = $this->cityRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

}
