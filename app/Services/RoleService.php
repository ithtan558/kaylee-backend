<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;

class RoleService extends BaseService
{
    protected $roleRep;

    public function __construct(RoleRepository $cityRep)
    {
        $this->roleRep = $cityRep;
    }

    public function getAll()
    {

        $data = $this->roleRep->getAll();

        $this->setData($data);

        return $this->getResponseData();
    }

}
