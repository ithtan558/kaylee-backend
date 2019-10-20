<?php

namespace App\Services;

use App\Repositories\ServiceCategoryRepository;
use Illuminate\Http\Request;

class ServiceCategoryService extends BaseService
{
    protected $serviceCategoryRep;

    public function __construct(ServiceCategoryRepository $serviceCategoryRep)
    {
        $this->serviceCategoryRep = $serviceCategoryRep;
    }

    public function getAll()
    {
        $data = $this->serviceCategoryRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

}
