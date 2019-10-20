<?php

namespace App\Services;

use App\Http\Validators\OutletMaster\BrandCreateValidator;
use App\Libraries\Api;
use App\Repositories\BrandRepository;
use App\Repositories\BrandServiceRepository;
use Illuminate\Http\Request;

class BrandService extends BaseService
{
    protected $brandRep;
    protected $brandServiceRep;

    public function __construct(
        BrandRepository $brandRep,
        BrandServiceRepository $brandServiceRep
    )
    {
        $this->brandRep = $brandRep;
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
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data = $this->brandRep->getDetail($id);

        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'name' => $request['name'],
                'phone' => $request['phone'],
                'location' => $request['location'],
                'city_id' => $request['city_id'],
                'district_id' => $request['district_id'],
                'is_active' => STATUS_ACTIVE,
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time']
            ];

            $name = $this->uploadImage($request);
            $dataCreate['image'] = $name;


            $this->brandRep->create($dataCreate);
            $this->setMessage('Created successfully.');
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
                'name' => $request['name'],
                'phone' => $request['phone'],
                'location' => $request['location'],
                'city_id' => $request['city_id'],
                'district_id' => $request['district_id'],
                'is_active' => STATUS_ACTIVE,
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time']
            ];

            $name = $this->uploadImage($request);
            $dataUpdate['image'] = $name;

            $this->brandRep->update($dataUpdate, $request['id']);
            $this->setMessage('Updated successfully.');
            $this->setData($dataUpdate);
        } catch (\Exception $ex) {
            $this->setMessage($ex->getMessage());
            $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    protected function uploadImage($request) {
        $name = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = $image->getClientOriginalName();
            $destinationPath = public_path('/upload/images');
            $image->move($destinationPath, $name);
        }
        return $name;
    }

}
