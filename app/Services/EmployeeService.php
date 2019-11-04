<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Libraries\Api;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeService extends BaseService
{
    protected $userRep;

    public function __construct(
        UserRepository $userRep
    )
    {
        $this->userRep = $userRep;
    }

    public function getAll()
    {
        $data = $this->userRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request)
    {
        $data = $this->userRep->getList($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getByPhone(Request $request)
    {
        $data = $this->userRep->getByPhone($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data = $this->userRep->getDetail($id);

        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'  => $this->getCurrentUser('client_id'),
                'name'       => $request['name'],
                'phone'      => $request['phone'],
                'email'      => $request['email'],
                'birthday'   => $request['birthday'],
                'created_by' => $this->getCurrentUser('id')
            ];

            $name = CommonHelper::uploadImage($request);
            if ($name) {
                $dataCreate['image'] = $name;
            }

            $this->userRep->create($dataCreate);
            $this->setMessage('Tạo khách hàng thành công');
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
                'name'       => $request['name'],
                'phone'      => $request['phone'],
                'email'      => $request['email'],
                'birthday'   => $request['birthday'],
                'updated_by' => $this->getCurrentUser('id')
            ];

            $name = CommonHelper::uploadImage($request);
            if ($name) {
                $dataUpdate['image'] = $name;
            }

            $this->userRep->update($dataUpdate, $request['id']);
            $this->setMessage('Cập nhật khách hàng thành công');
            $this->setData($dataUpdate);
        } catch (\Exception $ex) {
            $this->setMessage($ex->getMessage());
            $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->userRep->destroy($id);
        $this->setMessage('Xóa khách hàng thành công');
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
