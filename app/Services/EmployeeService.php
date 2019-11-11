<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeeService extends BaseService
{
    protected $userRep;
    protected $userRoleRep;

    public function __construct(
        UserRepository $userRep,
        UserRoleRepository $userRoleRep
    )
    {
        $this->userRep     = $userRep;
        $this->userRoleRep = $userRoleRep;
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

    public function getByPhoneOrName(Request $request)
    {
        $data = $this->userRep->getByPhoneOrName($request->all());
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

        $dataCreate = [
            'client_id'  => $this->getCurrentUser('client_id'),
            'name'       => $request['name'],
            'email'      => $request['email'],
            'phone'      => $request['phone'],
            'password'   => app('hash')->make($request['password']),
            'birthday'   => $request['birthday'],
            'gender'     => $request['gender'],
            'address'    => $request['address'],
            'brand_id'    => $request['brand_id'],
            'created_by' => $this->getCurrentUser('id')
        ];

        $name                = CommonHelper::uploadImage($request);
        $dataCreate['avatar'] = $name;

        $user = $this->userRep->create($dataCreate);

        $dataCreateRole = [
            'user_id' => $user->id,
            'role_id' => ROLE_EMPLOYEE
        ];
        $this->userRoleRep->create($dataCreateRole);

        $this->setMessage("Tạo nhân viên thành công");

        return $this->getResponseData();

    }

    public function update(Request $request)
    {

        $dataUpdate = [
            'client_id'  => $this->getCurrentUser('client_id'),
            'name'       => $request['name'],
            'email'      => $request['email'],
            'phone'      => $request['phone'],
            'password'   => app('hash')->make($request['password']),
            'birthday'   => $request['birthday'],
            'gender'     => $request['gender'],
            'address'    => $request['address'],
            'brand_id'    => $request['brand_id'],
            'created_by' => $this->getCurrentUser('id')
        ];

        $name                = CommonHelper::uploadImage($request);
        $dataUpdate['avatar'] = $name;

        $this->userRep->update($dataUpdate, $request['id']);

        $this->userRoleRep->deleteByUserId($request['id']);
        $dataCreateRole = [
            'user_id' => $request['id'],
            'role_id' => ROLE_EMPLOYEE
        ];
        $this->userRoleRep->create($dataCreateRole);

        $this->setMessage("Sửa nhân viên thành công");

        return $this->getResponseData();

    }

    public function delete($id)
    {
        $this->userRep->destroy($id);
        $this->setMessage('Xóa nhân viên thành công');
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
