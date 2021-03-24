<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Libraries\Api;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

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
        foreach ($data['items'] as &$item) {
            if (!empty($item->image)) {
                $item->image = PATH_IMAGE . $item->image;
            }
        }
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
        $data->image = PATH_IMAGE . $data->image;
        $this->setCityDistrictWards($data);

        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'        => $this->getCurrentUser('client_id'),
                'type_id'            => CUSTOMER_NORMAL,
                'name'        => $request['name'],
                'birthday'         => $request['birthday'],
                'hometown_city_id' => $request['hometown_city_id'],
                'address'          => $request['address'],
                'city_id'          => $request['city_id'],
                'district_id'      => $request['district_id'],
                'wards_id'         => $request['wards_id'],
                'phone'            => $request['phone'],
                'email'            => $request['email'],
                'created_by'       => $this->getCurrentUser('id')
            ];

            $name = CommonHelper::uploadImage($request);
            if ($name) {
                $dataCreate['image'] = $name;
            }

            $this->customerRep->create($dataCreate);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Tạo khách hàng thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo khách hàng thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function update(Request $request)
    {
        try {
            $dataUpdate = [
                'client_id'        => $this->getCurrentUser('client_id'),
                'name'        => $request['name'],
                'birthday'         => $request['birthday'],
                'hometown_city_id' => $request['hometown_city_id'],
                'address'          => $request['address'],
                'city_id'          => $request['city_id'],
                'district_id'      => $request['district_id'],
                'wards_id'         => $request['wards_id'],
                'phone'            => $request['phone'],
                'email'            => $request['email'],
                'created_by'       => $this->getCurrentUser('id')
            ];

            $name = CommonHelper::uploadImage($request);
            if ($name) {
                $dataUpdate['image'] = $name;
            }

            $this->customerRep->update($dataUpdate, $request['id']);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật khách hàng thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Cập nhật khách hàng thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->customerRep->update(['is_delete' => 1], $id);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa khách hàng thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
