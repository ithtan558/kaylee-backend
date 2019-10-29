<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
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

    public function __construct(UserRepository $userRep, UserRoleRepository $userRoleRep)
    {
        $this->userRep     = $userRep;
        $this->userRoleRep = $userRoleRep;
    }

    public function login(Request $request)
    {
        $field       = filter_var($request->all(), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
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

    public function create()
    {
        $dataCreate = [
            [
                'is_master'         => 0,
                'roles_id'          => 7,
                'username'          => 'phd-internal',
                'email'             => 'phd-internal@gmail.com',
                'password'          => app('hash')->make('phd1234@'),
                'original_password' => 'phd1234@',
                'first_name'        => 'PHD',
                'last_name'         => 'Internal',
                'full_name'         => 'PHD Internal',
                'birthday'          => '1995-01-01',
                'phone'             => '012345678',
                'address'           => 'Jakata',
            ]
        ];
        foreach ($dataCreate as $data) {

            $this->userRep->create($data);
        }
    }
}
