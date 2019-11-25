<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\ServiceCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function getList(Request $request)
    {
        $data = $this->serviceCategoryRep->getList($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data            = $this->serviceCategoryRep->getDetail($id);
        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'   => $this->getCurrentUser('client_id'),
                'name'        => $request['name'],
                'is_active'   => STATUS_ACTIVE,
                'created_by'  => $this->getCurrentUser('id')
            ];

            $this->serviceCategoryRep->create($dataCreate);

            $this->setMessage('Tạo loại dịch vụ thành công');
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
                'is_active'   => STATUS_ACTIVE,
                'updated_by'  => $this->getCurrentUser('id')
            ];

            $this->serviceCategoryRep->update($dataUpdate, $request['id']);

            $this->setMessage('Cập nhật loại dịch vụ thành công');
            $this->setData($dataUpdate);
        } catch (\Exception $ex) {
            $this->setMessage($ex->getMessage());
            $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->serviceCategoryRep->destroy($id);
        $this->setMessage('Xóa loại dịch vụ thành công');
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
