<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\BrandRepository;
use App\Repositories\BrandServiceRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class BrandService extends BaseService
{
    protected $brandRep;
    protected $brandServiceRep;

    public function __construct(
        BrandRepository $brandRep,
        BrandServiceRepository $brandServiceRep
    )
    {
        $this->brandRep        = $brandRep;
        $this->brandServiceRep = $brandServiceRep;
    }

    public function getAll()
    {

        $data = $this->brandRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request)
    {
        $data = $this->brandRep->getList($request->all());
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
        $data        = $this->brandRep->getDetail($id);
        $data->image = PATH_IMAGE . $data->image;
        $this->setCityDistrictWards($data);

        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'   => $this->getCurrentUser('client_id'),
                'name'        => $request['name'],
                'phone'       => $request['phone'],
                'location'    => $request['location'],
                'city_id'     => $request['city_id'],
                'district_id' => $request['district_id'],
                'wards_id'    => $request['wards_id'],
                'is_active'   => STATUS_ACTIVE,
                'start_time'  => $request['start_time'],
                'end_time'    => $request['end_time'],
                'created_by'  => $this->getCurrentUser('id')
            ];

            $name                = CommonHelper::uploadImage($request, 460, 460);
            $dataCreate['image'] = $name;

            $this->brandRep->create($dataCreate);

            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Tạo chi nhánh thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo chi nhánh thất bại';
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
                'phone'       => $request['phone'],
                'location'    => $request['location'],
                'city_id'     => $request['city_id'],
                'district_id' => $request['district_id'],
                'wards_id'    => $request['wards_id'],
                'is_active'   => STATUS_ACTIVE,
                'start_time'  => $request['start_time'],
                'end_time'    => $request['end_time'],
                'updated_by'  => $this->getCurrentUser('id')
            ];

            if ($request->hasFile('image')) {
                $name                = CommonHelper::uploadImage($request, 460, 460);
                $dataUpdate['image'] = $name;
            }

            $this->brandRep->update($dataUpdate, $request['id']);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật chi nhánh thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Cập nhật chi nhánh thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->brandRep->update(['is_delete' => 1], $id);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa chi nhánh thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
