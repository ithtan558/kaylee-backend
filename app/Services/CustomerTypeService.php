<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\CustomerTypeRepository;

class CustomerTypeService extends BaseService
{
    protected $customerTypeRep;

    public function __construct(
        CustomerTypeRepository $customerTypeRep
    )
    {
        $this->customerTypeRep = $customerTypeRep;
    }

    public function getAll()
    {

        $data = $this->customerTypeRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

}
