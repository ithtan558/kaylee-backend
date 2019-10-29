<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\ServiceRepository;
use App\Repositories\BrandServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceService extends BaseService
{
    protected $serviceRep;
    protected $brandServiceRep;

    public function __construct(
        ServiceRepository $serviceRep,
        BrandServiceRepository $brandServiceRep
    )
    {
        $this->serviceRep      = $serviceRep;
        $this->brandServiceRep = $brandServiceRep;
    }

    public function getAll()
    {
        $data = $this->serviceRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request)
    {
        $data = $this->serviceRep->getList($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data            = $this->serviceRep->getDetail($id);
        $brandServices   = $this->brandServiceRep->getByServiceId($id)->pluck('brand_id');
        $data->brand_ids = $brandServices;
        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'name'        => $request['name'],
                'code'        => $request['code'],
                'description' => $request['description'],
                'category_id' => $request['category_id'],
                'time'        => $request['time'],
                'price'       => $request['price'],
                'is_active'   => STATUS_ACTIVE,
                'created_by'  => $this->getCurrentUser('id')
            ];

            $name                = CommonHelper::uploadImage($request);
            $dataCreate['image'] = $name;

            $service = $this->serviceRep->create($dataCreate);
            // Insert brand service table
            $arr_brand = explode(',', $request['brand_ids']);
            foreach ($arr_brand as $brand) {
                $dataCreateBrandService = [
                    'brand_id'   => $brand,
                    'service_id' => $service->id
                ];
                $this->brandServiceRep->create($dataCreateBrandService);
            }

            $this->setMessage('Tạo dịch vụ thành công');
            $this->setData($dataCreate);
        } catch (\Exception $ex) {
            $this->setMessage($ex->getMessage());
            $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function update(Request $request)
    {
        try {
            $dataUpdate = [
                'name'        => $request['name'],
                'code'        => $request['code'],
                'description' => $request['description'],
                'category_id' => $request['category_id'],
                'time'        => $request['time'],
                'price'       => $request['price'],
                'is_active'   => STATUS_ACTIVE,
                'updated_by'  => $this->getCurrentUser('id')
            ];

            $name                = $this->uploadImage($request);
            $dataCreate['image'] = $name;

            $this->serviceRep->update($dataUpdate, $request['id']);

            // Delete all brand service of this service first

            $this->brandServiceRep->deleteByServiceId($request['id']);
            // Insert brand service table
            $arr_brand = explode(',', $request['brand_ids']);
            foreach ($arr_brand as $brand) {
                $dataCreateBrandService = [
                    'brand_id'   => $brand,
                    'service_id' => $request['id']
                ];
                $this->brandServiceRep->create($dataCreateBrandService);
            }

            $this->setMessage('Cập nhật dịch vụ thành công');
            $this->setData($dataCreate);
        } catch (\Exception $ex) {
            $this->setMessage($ex->getMessage());
            $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->serviceRep->destroy($id);
        $this->setMessage('Xóa dịch vụ thành công');
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
