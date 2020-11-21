<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\ServiceRepository;
use App\Repositories\BrandServiceRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

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
        foreach ($data['items'] as &$item) {
            if (!empty($item->image)) {
                $item->image = PATH_IMAGE . $item->image;
            }
        }
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data          = $this->serviceRep->getDetail($id);
        $brandServices = $this->brandServiceRep->getByServiceId($id);
        $brands        = [];
        foreach ($brandServices as $brandService) {
            $obj       = new stdClass();
            $obj->id   = $brandService->brand_id;
            $obj->name = $brandService->brand_name;
            $brands[]  = $obj;
        }
        $data->brands = $brands;

        // Category
        $obj            = new stdClass();
        $obj->id        = $data->category_id;
        $obj->name      = $data->category_name;
        $data->category = $obj;

        unset($data->category_id);

        $data->image = PATH_IMAGE . $data->image;

        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'   => $this->getCurrentUser('client_id'),
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
                    'service_id' => $service->id,
                    'is_active'   => STATUS_ACTIVE,
                ];
                $this->brandServiceRep->create($dataCreateBrandService);
            }
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Tạo dịch vụ thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo loại dịch vụ thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $name                = CommonHelper::uploadImage($request);
            if ($name) {
                $dataUpdate['image'] = $name;
            }

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

            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật dịch vụ thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo loại dịch vụ thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->serviceRep->update(['is_delete' => 1], $id);
        $this->brandServiceRep->updateByMultipleCondition(['is_delete' => 1], ['service_id' => $id]);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa dịch vụ thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
