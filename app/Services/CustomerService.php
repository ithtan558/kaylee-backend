<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Libraries\Api;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerService extends BaseService
{
    protected $customerRep;

    public function __construct(
        CustomerRepository $customerRep
    )
    {
        $this->customerRep = $customerRep;
    }

    public function getAll()
    {
        $data = $this->customerRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getCount()
    {
        $data = $this->customerRep->getCount();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request)
    {
        $data = $this->customerRep->getList($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getByPhoneOrName(Request $request)
    {
        $data = $this->customerRep->getByPhoneOrName($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data = $this->customerRep->getDetail($id);

        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'  => $this->getCurrentUser('client_id'),
                'type_id'       => $request['type_id'],
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

            $this->customerRep->create($dataCreate);
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
                'type_id'       => $request['type_id'],
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

            $this->customerRep->update($dataUpdate, $request['id']);
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
        $this->customerRep->destroy($id);
        $this->setMessage('Xóa khách hàng thành công');
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
