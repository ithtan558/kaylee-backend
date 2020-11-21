<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\CampaignRepository;
use Illuminate\Http\Request;

class CampaignService extends BaseService
{
    protected $campaignRep;

    public function __construct(CampaignRepository $campaignRep)
    {
        $this->campaignRep = $campaignRep;
    }

    public function getAll()
    {

        $data = $this->campaignRep->getAll();

        $this->setData($data);

        return $this->getResponseData();
    }

}
