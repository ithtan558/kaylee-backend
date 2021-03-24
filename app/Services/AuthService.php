<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Libraries\Curl;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\ClientRepository;
use App\Repositories\BrandRepository;
use App\Repositories\OtpRepository;
use App\Repositories\CityRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\WardsRepository;
use App\Repositories\UserDeviceRepository;
use App\Repositories\AdminUsersRepository;
use Illuminate\Http\Response;
use stdClass;
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
    protected $brandRep;
    protected $otpRep;
    protected $cityRep;
    protected $districtRep;
    protected $wardsRep;
    protected $userDeviceRep;
    protected $adminUsersRep;

    public function __construct(
        UserRepository $userRep,
        UserRoleRepository $userRoleRep,
        ClientRepository $clientRep,
        BrandRepository $brandRep,
        OtpRepository $otpRep,
        CityRepository $cityRep,
        DistrictRepository $districtRep,
        WardsRepository $wardsRep,
        UserDeviceRepository $userDeviceRep,
        AdminUsersRepository $adminUsersRep
    )
    {
        $this->userRep       = $userRep;
        $this->userRoleRep   = $userRoleRep;
        $this->clientRep     = $clientRep;
        $this->brandRep      = $brandRep;
        $this->otpRep        = $otpRep;
        $this->cityRep       = $cityRep;
        $this->districtRep   = $districtRep;
        $this->wardsRep      = $wardsRep;
        $this->userDeviceRep = $userDeviceRep;
        $this->adminUsersRep = $adminUsersRep;
    }

    public function login(Request $request)
    {
        $field       = filter_var($request->all(), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [
            $field      => $request['account'],
            'password'  => $request['password'],
            'is_active' => STATUS_ACTIVE
        ];

        if (!$token = JWTAuth::attempt($credentials, ['exp' => 1])) {
            $errors          = new stdClass();
            $errors->title   = 'Đăng nhập thất bại';
            $errors->message = 'Số điện thoại hoặc mật khẩu không có thực. Vui lòng nhập lại.';
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_BAD_REQUEST);
            return $this->getResponseData();
        }

        $auth = JWTAuth::user();

        if ($auth->is_active != STATUS_ACTIVE) {
            $errors          = new stdClass();
            $errors->title   = 'Đăng nhập thất bại';
            $errors->message = 'Tài khoản của bạn đã bị vô hiệu hóa, xin vui lòng liên hệ với ban quản trị.';
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_UNAUTHORIZED);
            return $this->getResponseData();
        }
        $msg          = new stdClass();
        $msg->title   = 'Đăng nhập thành công';
        $msg->content = 'Đăng nhập thành công.';
        $this->setMessage($msg);

        $role = $this->userRoleRep->getRoleByUserId($auth->id)->pluck('code');

        // Insert device
        $query_device = $this->userDeviceRep->findByAttributes(['token' => $request['token'], 'user_id' => $auth->id]);
        if (count($query_device) == 0) {
            $insertUserDevice = [
                'client_id' => $auth->client_id,
                'user_id'   => $auth->id,
                'token'     => $request['token']
            ];
            $this->userDeviceRep->create($insertUserDevice);
        }

        unset($auth->client_id);
        unset($auth->token_reset_password);
        unset($auth->city_id);
        unset($auth->hometown_city_id);
        unset($auth->district_id);
        unset($auth->wards_id);
        unset($auth->is_active);
        unset($auth->is_delete);
        unset($auth->created_at);
        unset($auth->updated_at);
        unset($auth->created_by);
        unset($auth->updated_by);
        $auth->image   = PATH_IMAGE . $auth->image;
        $auth->role_id = (int)$role[0];

        $this->setData([
            'token'     => $token,
            'user_info' => $auth,
        ]);

        return $this->getResponseData();
    }

    public function verifyPhoneAndSendOtp(Request $request)
    {

        $user = $this->userRep->verifyByPhone($request['phone']);
        if (empty($user)) {
            $errors          = new stdClass();
            $errors->title   = 'Nhập số điện thoại thất bại';
            $errors->message = 'Số điện thoại không đúng. Vui lòng nhập lại.';
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_BAD_REQUEST);
            return $this->getResponseData();
        }
        // Send otp

        $otp = $this->otpRep->verifyByUser($user->id);
        if (!empty($otp) && !$otp->is_verify) {
            $dataUpdateOtp = [
                'is_delete' => STATUS_ACTIVE
            ];
            $this->otpRep->update($dataUpdateOtp, $otp->id);
        }
        $dataCreateOtp = [
            'otp'       => DEFAULT_NUMBER_OTP,
            'type'      => TYPE_OTP_FORGOT,
            'user_id'   => $user->id,
            'is_verify' => STATUS_INACTIVE
        ];
        $this->otpRep->create($dataCreateOtp);

        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Hệ thống đã gửi mã xác thực qua số điện thoại, xin vui lòng nhập ở bước tiếp theo';
        $this->setMessage($msg);
        $this->setData([
            'user_id' => $user->id,
        ]);

        return $this->getResponseData();

    }

    public function verifyOtp(Request $request)
    {

        $otp                  = $this->otpRep->verifyByOtp($request->all(), TYPE_OTP_FORGOT);
        $token_reset_password = md5(uniqid(rand(), true));
        if (!empty($otp)) {
            $dataUpdateOtp = [
                'is_verify' => STATUS_ACTIVE
            ];
            $this->otpRep->update($dataUpdateOtp, $otp->id);
            // Update token reset password for user
            $dataUpdateUser = [
                'token_reset_password' => $token_reset_password
            ];
            $this->userRep->update($dataUpdateUser, $otp->user_id);
        } else {
            $errors          = new stdClass();
            $errors->title   = 'Chứng thực thất bại';
            $errors->message = 'Mã OTP không đúng';

            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_BAD_REQUEST);
            return $this->getResponseData();
        }
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xác thực mã thành công';
        $this->setMessage($msg);
        $this->setData(['token_reset_password' => $token_reset_password, 'user_id' => $otp->user_id]);

        return $this->getResponseData();

    }

    public function verifyOtpForRegister(Request $request)
    {

        $otp = $this->otpRep->verifyByOtp($request->all(), TYPE_OTP_REGISTER);
        if (!empty($otp)) {
            $dataUpdateOtp = [
                'is_verify' => STATUS_ACTIVE
            ];
            $this->otpRep->update($dataUpdateOtp, $otp->id);
            $user = $this->userRep->find($otp->user_id);
            // Update data for user
            $this->clientRep->updateByMultipleCondition(['is_active' => STATUS_ACTIVE], ['id' => $user->client_id]);
            $this->brandRep->updateByMultipleCondition(['is_active' => STATUS_ACTIVE], ['id' => $user->brand_id]);
            $this->userRep->updateByMultipleCondition(['is_active' => STATUS_ACTIVE], ['id' => $user->id]);
            $this->userRoleRep->updateByMultipleCondition(['is_active' => STATUS_ACTIVE], ['user_id' => $user->id]);
        } else {
            $errors          = new stdClass();
            $errors->title   = 'Chứng thực thất bại';
            $errors->message = 'Mã OTP không đúng';

            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_BAD_REQUEST);
            return $this->getResponseData();
        }
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xác thực mã thành công';
        $this->setMessage($msg);
        $this->setData(['user_id' => $otp->user_id]);

        return $this->getResponseData();

    }

    public function updatePassword(Request $request)
    {

        $user = $this->userRep->findByAttributes(['id' => $request['user_id'], 'token_reset_password' => $request['token_reset_password']]);
        if (count($user) > 0) {
            $dataUpdatePassword = [
                'password' => app('hash')->make($request['password'])
            ];
            $this->userRep->update($dataUpdatePassword, $user[0]->id);
        } else {
            $errors          = new stdClass();
            $errors->title   = 'Chứng thực thất bại';
            $errors->message = 'Không tìm thấy tài khoản.';

            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_BAD_REQUEST);
            return $this->getResponseData();
        }
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Thay đổi mật khẩu thành công';
        $this->setMessage($msg);

        return $this->getResponseData();

    }

    public function logout()
    {
        $token = JWTAuth::getToken();

        try {
            $user   = JWTAuth::user();
            $params = ['user_id' => $user ? $user->id : 0];

            JWTAuth::setToken($token)->invalidate();
        } catch (TokenInvalidException $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Chứng thực thất bại';
            $errors->message = 'Token không khả dụng.';

            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_UNAUTHORIZED);
            return $this->getResponseData();
        }
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Đăng xuất thành công';
        $this->setMessage($msg);

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
        $role = $this->userRoleRep->getRoleByUserId($user->id)->pluck('code');
        // Transform city, district, wards
        $this->setCityDistrictWards($user);

        unset($user->client_id);
        unset($user->first_name);
        unset($user->last_name);
        unset($user->client_id);
        unset($user->token_reset_password);
        unset($user->city_id);
        unset($user->hometown_city_id);
        unset($user->district_id);
        unset($user->wards_id);
        unset($user->is_active);
        unset($user->is_delete);
        unset($user->created_at);
        unset($user->updated_at);
        unset($user->created_by);
        unset($user->updated_by);

        $user->role_id = (int)$role[0];

        $user->image = PATH_IMAGE . $user->image;
        $this->setData($user);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {

        // Create client
        $dataCreateClient = [
            'name'      => $request['name'],
            'phone'     => $request['phone'],
            'is_active' => STATUS_INACTIVE
        ];
        // Check and get admin_user_id
        if (!empty($request['code'])) {
            $admin_user = $this->adminUsersRep->findByAttributes(['code' => $request['code']]);
            if (count($admin_user) > 0) {
                $dataCreateClient['admin_user_id'] = $admin_user[0]->id;
            }
        }
        $client = $this->clientRep->create($dataCreateClient);

        // Create Brand
        $dataCreateBrand = [
            'client_id'  => $client->id,
            'name'       => NAME_REGISTER,
            'phone'      => $request['phone'],
            'start_time' => START_TIME,
            'end_time'   => END_TIME,
            'is_active'  => STATUS_INACTIVE
        ];
        $brand           = $this->brandRep->create($dataCreateBrand);

        // Create user
        $dataCreateUser = [
            'client_id' => $client->id,
            'brand_id'  => $brand->id,
            'name'      => $request['name'],
            'email'     => $request['email'],
            'phone'     => $request['phone'],
            'password'  => app('hash')->make($request['password']),
            'is_active' => STATUS_INACTIVE
        ];
        $user           = $this->userRep->create($dataCreateUser);

        $dataCreateRole = [
            'user_id'   => $user->id,
            'role_id'   => ROLE_MANAGER,
            'is_active' => STATUS_INACTIVE
        ];
        $this->userRoleRep->create($dataCreateRole);

        // Send otp
        $otp           = CommonHelper::createRandomOtp(4);
        $dataCreateOtp = [
            'otp'       => $otp,
            'user_id'   => $user->id,
            'is_verify' => STATUS_INACTIVE,
            'type'      => TYPE_OTP_REGISTER,
            'phone'     => $request['phone']
        ];
        $this->otpRep->create($dataCreateOtp);
        // Send otp
        $dataOtp = [
            'u'     => USERNAME,
            'pwd'   => PASSWORD,
            'from'  => FROM,
            'phone' => $request['phone'],
            'sms'   => 'KAYLEE-Ma OTP cua ban la ' . $otp . '. Vui long nhap ma de tiep tuc',
            'bid'   => CommonHelper::createRandomCode(),
            'type'  => TYPE,
            'json'  => JSON,
        ];
        Curl::callApi(LINK_OTP, $dataOtp);

        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Hệ thống đã gửi mã xác thực qua số điện thoại, xin vui lòng nhập ở bước tiếp theo';
        $this->setMessage($msg);
        $this->setData([
            'user_id' => $user->id
        ]);

        return $this->getResponseData();

    }

    public function update(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        // Check correct account
        if (!empty($user)) {
            // Create user
            $dataCreateUser          = [
                'name'        => $request['name'],
                'address'     => $request['address'],
                'birthday'    => $request['birthday'],
                'city_id'     => $request['city_id'],
                'district_id' => $request['district_id'],
                'wards_id'    => $request['wards_id'],
            ];
            $name                    = CommonHelper::uploadImage($request);
            $dataCreateUser['image'] = $name;
            $this->userRep->update($dataCreateUser, $user->id);

            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật tài khoản thành công';
            $this->setMessage($msg);
        } else {
            $errors          = new stdClass();
            $errors->title   = 'Chứng thực thất bại';
            $errors->message = 'Token không khả dụng.';

            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_UNAUTHORIZED);
        }

        return $this->getResponseData();

    }
}
