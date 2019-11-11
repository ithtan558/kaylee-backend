<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\BrandRepository;
use App\Repositories\BrandServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        // Filter base on roles of user
        $user = CommonHelper::getAuth();
        $roles = [];
        $reject_roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }
        if (in_array(ROLE_SUPERADMIN, $roles)) {
            $reject_roles = [ROLE_SUPERADMIN];
        } else if (in_array(ROLE_MANAGER, $roles)) {
            $reject_roles = [ROLE_SUPERADMIN, ROLE_MANAGER];
        } else if (in_array(ROLE_BRAND_MANAGER, $roles)) {
            $reject_roles = [ROLE_SUPERADMIN, ROLE_MANAGER, ROLE_BRAND_MANAGER];
        }

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
                'client_id'   => $this->getCurrentUser('client_id'),
                'name'        => $request['name'],
                'phone'       => $request['phone'],
                'location'    => $request['location'],
                'city_id'     => $request['city_id'],
                'district_id' => $request['district_id'],
                'is_active'   => STATUS_ACTIVE,
                'start_time'  => $request['start_time'],
                'end_time'    => $request['end_time'],
                'created_by'  => $this->getCurrentUser('id')
            ];

            $name                = CommonHelper::uploadImage($request);
            $dataCreate['image'] = $name;

            $this->brandRep->create($dataCreate);
            $this->setMessage('Tạo chi nhánh thành công');
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
                'phone'       => $request['phone'],
                'location'    => $request['location'],
                'city_id'     => $request['city_id'],
                'district_id' => $request['district_id'],
                'is_active'   => STATUS_ACTIVE,
                'start_time'  => $request['start_time'],
                'end_time'    => $request['end_time'],
                'updated_by'  => $this->getCurrentUser('id')
            ];

            $name                = CommonHelper::uploadImage($request);
            $dataUpdate['image'] = $name;

            $this->brandRep->update($dataUpdate, $request['id']);
            $this->setMessage('Cập nhật chi nhánh thành công');
            $this->setData($dataUpdate);
        } catch (\Exception $ex) {
            $this->setMessage($ex->getMessage());
            $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->brandRep->destroy($id);
        $this->setMessage('Xóa chi nhánh thành công');
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
