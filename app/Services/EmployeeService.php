<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class EmployeeService extends BaseService
{
    protected $userRep;
    protected $userRoleRep;
    protected $roleRep;

    public function __construct(
        UserRepository $userRep,
        UserRoleRepository $userRoleRep,
        RoleRepository $roleRep
    )
    {
        $this->userRep     = $userRep;
        $this->userRoleRep = $userRoleRep;
        $this->roleRep     = $roleRep;
    }

    public function getAll()
    {
        $data = $this->userRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request)
    {
        $data       = $this->userRep->getList($request->all());
        $roles      = $this->roleRep->getAll()->keyBy('id')->toArray();
        $user_roles = $this->userRoleRep->all()->keyBy('user_id')->toArray();
        foreach ($data['items'] as &$item) {
            if (!empty($item->image)) {
                $item->image = PATH_IMAGE . $item->image;
            }
            if (isset($user_roles[$item->id])) {
                $user_role = $user_roles[$item->id];
                if (isset($user_role['role_id']) && isset($roles[$user_role['role_id']])) {
                    $role       = $roles[$user_role['role_id']];
                    $obj        = new stdClass();
                    $obj->id    = $role['id'];
                    $obj->name  = $role['name'];
                    $item->role = $obj;
                }
            }
        }
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getByPhoneOrName(Request $request)
    {
        $data = $this->userRep->getByPhoneOrName($request->all());
        $roles = $this->roleRep->getAll()->keyBy('id');
        foreach ($data as &$item) {
            $obj = new stdClass();
            $user_roles = $item->user_roles;
            $obj->id = $user_roles[0]->id;
            $obj->name = $roles[$user_roles[0]->role_id]->name;
            $item->role = $obj;
            unset($item->user_roles);
        }
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data        = $this->userRep->getDetail($id);
        $data->image = PATH_IMAGE . $data->image;
        $this->setCityDistrictWards($data);

        // Brand
        $obj         = new stdClass();
        $obj->id     = $data->brand_id;
        $obj->name   = $data->brand_name;
        $data->brand = $obj;

        unset($data->brand_id);
        unset($data->brand_name);

        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {

        $dataCreate = [
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
            'brand_id'         => $request['brand_id'],
            'created_by'       => $this->getCurrentUser('id'),
            'password'         => app('hash')->make($request['password'])
        ];

        $name                = CommonHelper::uploadImage($request);
        $dataCreate['image'] = $name;

        $user = $this->userRep->create($dataCreate);

        $dataCreateRole = [
            'user_id' => $user->id,
            'role_id' => $request['role_id']
        ];
        $this->userRoleRep->create($dataCreateRole);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Tạo nhân viên thành công';
        $this->setMessage($msg);

        return $this->getResponseData();

    }

    public function update(Request $request)
    {

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
            'brand_id'         => $request['brand_id'],
            'created_by'       => $this->getCurrentUser('id')
        ];

        if ($request->hasFile('image')) {
            $name                = CommonHelper::uploadImage($request);
            $dataUpdate['image'] = $name;
        }

        if (!empty($request['password'])) {
            $dataUpdate['password'] = app('hash')->make($request['password']);
        }

        $this->userRep->update($dataUpdate, $request['id']);

        $this->userRoleRep->deleteByUserId($request['id']);
        $dataCreateRole = [
            'user_id' => $request['id'],
            'role_id' => $request['role_id']
        ];
        $this->userRoleRep->create($dataCreateRole);

        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Cập nhật nhân viên thành công';
        $this->setMessage($msg);

        return $this->getResponseData();

    }

    public function delete($id)
    {
        $this->userRep->update(['is_delete' => 1], $id);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa nhân viên thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
