<?php

namespace App\Services;

use App\Repositories\AdsRepository;

class AdsService extends BaseService
{
    protected $adsRep;

    public function __construct(
        AdsRepository $adsRep
    )
    {
        $this->adsRep        = $adsRep;
    }

    public function getAll()
    {

        $data = $this->adsRep->getAll();
        foreach ($data as &$item) {
            if (!empty($item['image'])) {
                $item['image'] = PATH_IMAGE_ADS . $item['image'];
            }
        }
        $this->setData($data);

        return $this->getResponseData();
    }

}
