<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\ClientRepository;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\Request;

class AuthService extends BaseService
{
    protected $userRep;
    protected $userRoleRep;
    protected $clientRep;

    public function __construct(
        UserRepository $userRep,
        UserRoleRepository $userRoleRep,
        ClientRepository $clientRep
    )
    {
        $this->userRep     = $userRep;
        $this->userRoleRep = $userRoleRep;
        $this->clientRep   = $clientRep;
    }

    public function login(Request $request)
    {
        $field       = filter_var($request->all(), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [
            $field     => $request['account'],
            'password' => $request['password']
        ];

        if (!$token = JWTAuth::attempt($credentials, ['exp' => 84600])) {
            $msg = 'Tài khoản và mật khẩu không đúng';
            abort(Response::HTTP_BAD_REQUEST, $msg);
        }

        $auth = JWTAuth::user();

        if ($auth->is_active != STATUS_ACTIVE) {
            $msg = 'Tài khoản của bạn đã bị vô hiệu hóa, xin vui lòng liên hệ với ban quản trị';
            abort(Response::HTTP_UNAUTHORIZED, $msg);
        }

        $this->setMessage('Đăng nhập thành công');
        $this->setData([
            'token'     => $token,
            'user_info' => $auth,
        ]);

        return $this->getResponseData();
    }

    public function logout()
    {
        $token  = JWTAuth::getToken();
        $params = [];

        try {
            $user   = JWTAuth::user();
            $params = ['user_id' => $user ? $user->id : 0];

            JWTAuth::setToken($token)->invalidate();
        } catch (TokenInvalidException $ex) {
            abort(Response::HTTP_UNAUTHORIZED, 'Token is invalid');
        }

        $this->setMessage("Đăng xuất thành công");

        return $this->getResponseData();
    }

    public function getAuthenticatedUser()
    {
        $user = '';
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                abort(Response::HTTP_UNAUTHORIZED, 'Không tìm thấy User');
            }
        } catch (TokenExpiredException $ex) {
            abort(Response::HTTP_UNAUTHORIZED, 'Token expired');
        } catch (TokenInvalidException $ex) {
            abort(Response::HTTP_UNAUTHORIZED, 'Token is invalid');
        } catch (JWTException $ex) {
            abort(Response::HTTP_UNAUTHORIZED, $ex->getMessage());
        }

        // Get roles
        $roles       = $this->userRoleRep->getRoleByUserId($user->id)->pluck('code');
        $user->roles = $roles;
        $this->setData($user);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {

        // Create brand
        $dataCreateBrand = [
            'name'        => $request['name_client'],
            'phone'       => $request['phone_client'],
            'location'    => $request['location_client'],
            'city_id'     => $request['city_id'],
            'district_id' => $request['district_id']
        ];
        $client          = $this->clientRep->create($dataCreateBrand);

        $dataCreateUser = [
            'client_id' => $client->id,
            'name'      => $request['name'],
            'email'     => $request['email'],
            'phone'     => $request['phone'],
            'password'  => app('hash')->make($request['password'])
        ];
        $user           = $this->userRep->create($dataCreateUser);

        $dataCreateRole = [
            'user_id' => $user->id,
            'role_id' => 1
        ];
        $this->userRoleRep->create($dataCreateRole);

        $this->setMessage("Tạo cửa hàng thành công");

        return $this->getResponseData();

    }
}
