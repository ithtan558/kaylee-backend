<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\ServiceCategoryRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

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
        $data = $this->serviceCategoryRep->getDetail($id);
        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'  => $this->getCurrentUser('client_id'),
                'name'       => $request['name'],
                'code'       => $request['code'],
                'sequence'   => $request['sequence'],
                'is_active'  => STATUS_ACTIVE,
                'created_by' => $this->getCurrentUser('id')
            ];

            $this->serviceCategoryRep->create($dataCreate);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Tạo loại dịch vụ thành công';
            $this->setMessage($msg);
            $this->setData($dataCreate);
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
                'name'       => $request['name'],
                'code'       => $request['code'],
                'sequence'   => $request['sequence'],
                'is_active'  => STATUS_ACTIVE,
                'updated_by' => $this->getCurrentUser('id')
            ];

            $this->serviceCategoryRep->update($dataUpdate, $request['id']);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật loại dịch vụ thành công';
            $this->setMessage($msg);
            $this->setData($dataUpdate);
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
        $this->serviceCategoryRep->update(['is_delete' => 1], $id);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa loại dịch vụ thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
