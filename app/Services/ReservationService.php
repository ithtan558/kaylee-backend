<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Libraries\Api;
use App\Repositories\ReservationRepository;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class ReservationService extends BaseService
{
    protected $reservationRep;
    protected $customerRep;

    public function __construct(
        ReservationRepository $reservationRep,
        CustomerRepository $customerRep
    )
    {
        $this->reservationRep = $reservationRep;
        $this->customerRep = $customerRep;
    }

    public function getAll()
    {
        $data = $this->reservationRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getCount()
    {
        $data = $this->reservationRep->getCount();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request)
    {
        $data = $this->reservationRep->getList($request->all());
        foreach ($data['items'] as &$item) {
            $obj = new stdClass();
            $obj->id = $item->brand_id;
            $obj->name = $item->brand_name;
            $item->brand = $obj;
            unset($item->brand_id);
            unset($item->brand_name);
            if (!empty($item->image)) {
                $item->image = PATH_IMAGE . $item->image;
            }
        }
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getByPhoneOrName(Request $request)
    {
        $data = $this->reservationRep->getByPhoneOrName($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data        = $this->reservationRep->getDetail($id);
        $brand       = new stdClass();
        $brand->id   = $data->brand_id;
        $brand->name = $data->brand_name;
        $data->brand = $brand;
        $this->setCityDistrictWards($data);

        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {

            // Check exist customer before by phone
            $customer_query = $this->customerRep->findByAttributes(['phone' => $request['phone']]);
            if (count($customer_query) == 0) {
                $dataInsertCustomer = [
                    'client_id'        => $this->getCurrentUser('client_id'),
                    'name'        => $request['name'],
                    'hometown_city_id' => isset($request['hometown_city_id']) ? $request['hometown_city_id'] : 0,
                    'city_id'          => isset($request['city_id']) ? $request['city_id'] : 0,
                    'district_id'      => isset($request['district_id']) ? $request['district_id'] : 0,
                    'wards_id'         => isset($request['wards_id']) ? $request['wards_id'] : 0,
                    'phone'            => isset($request['phone']) ? $request['phone'] : '',
                    'email'            => isset($request['email']) ? $request['email'] : '',
                    'type_id'          => CUSTOMER_NORMAL
                ];
                $customer           = $this->customerRep->create($dataInsertCustomer);

                $customer_id = $customer->id;
            } else {
                $customer_id = $customer_query[0]->id;
            }

            $dataCreate = [
                'client_id'   => $this->getCurrentUser('client_id'),
                'brand_id'    => $request['brand_id'],
                'customer_id' => $customer_id,
                'name'   => $request['name'],
                'address'     => $request['address'],
                'city_id'     => $request['city_id'],
                'district_id' => $request['district_id'],
                'wards_id'    => $request['wards_id'],
                'phone'       => $request['phone'],
                'quantity'    => $request['quantity'],
                'note'        => $request['note'],
                'datetime'    => $request['datetime'],
                'status'       => RESERVATION_BOOKED,
                'created_by'  => $this->getCurrentUser('id')
            ];

            $reservation = $this->reservationRep->create($dataCreate);
            $this->reservationRep->update([
                'code'     => CommonHelper::createRandomCode($reservation['id'])
            ],
                $reservation['id']
            );


            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Tạo lịch hẹn thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo lịch hẹn thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function update(Request $request)
    {
        try {
            $dataUpdate = [
                'client_id'   => $this->getCurrentUser('client_id'),
                'brand_id'    => $request['brand_id'],
                'name'   => $request['name'],
                'address'     => $request['address'],
                'city_id'     => $request['city_id'],
                'district_id' => $request['district_id'],
                'wards_id'    => $request['wards_id'],
                'phone'       => $request['phone'],
                'quantity'    => $request['quantity'],
                'note'        => $request['note'],
                'datetime'    => $request['datetime'],
                'status'       => RESERVATION_BOOKED,
                'created_by'  => $this->getCurrentUser('id')
            ];

            $this->reservationRep->update($dataUpdate, $request['id']);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật lịch hẹn thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Cập nhật lịch hẹn thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
        return $this->getResponseData();
    }

    public function updateStatus(Request $request)
    {
        try {
            $dataUpdate = [
                'status'       => $request['status'],
                'created_by'  => $this->getCurrentUser('id')
            ];

            $this->reservationRep->update($dataUpdate, $request['id']);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật lịch hẹn thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Cập nhật lịch hẹn thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->reservationRep->update(['is_delete' => 1], $id);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa khách hàng thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
