<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\VersionRepository;
use Illuminate\Http\Request;

class VersionService extends BaseService
{
    protected $versionRep;

    public function __construct(VersionRepository $versionRep)
    {
        $this->versionRep = $versionRep;
    }

    public function getDetail()
    {

        $data = $this->versionRep->getDetail();

        $this->setData($data);

        return $this->getResponseData();
    }

}
