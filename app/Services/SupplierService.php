<?php

namespace App\Services;

use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;

class SupplierService extends BaseService
{
    protected $supplierRep;

    public function __construct(SupplierRepository $supplierRep)
    {
        $this->supplierRep = $supplierRep;
    }

    public function getList($params)
    {
        $data = $this->supplierRep->getList($params);
        foreach ($data['items'] as &$item) {
            if (!empty($item->image)) {
                $item->image = PATH_IMAGE_SUPPLIER . $item->image;
            }
        }
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data = $this->supplierRep->getDetail($id);
        if (!empty($data)) {
            $data->image = PATH_IMAGE_SUPPLIER . $data->image;
        }

        $this->setData($data);

        return $this->getResponseData();
    }

}
