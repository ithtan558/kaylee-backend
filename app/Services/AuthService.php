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
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ServiceCategoryRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\BrandProductRepository;
use App\Repositories\BrandServiceRepository;
use Illuminate\Http\Response;
use stdClass;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\Request;
use App\Jobs\SendOtp;

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
    protected $productCategoryRep;
    protected $productRep;
    protected $serviceCategoryRep;
    protected $serviceRep;
    protected $brandProductRep;
    protected $brandServiceRep;

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
        AdminUsersRepository $adminUsersRep,
        ProductCategoryRepository $productCategoryRep,
        ProductRepository $productRep,
        ServiceCategoryRepository $serviceCategoryRep,
        ServiceRepository $serviceRep,
        BrandProductRepository $brandProductRep,
        BrandServiceRepository $brandServiceRep
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
        $this->productCategoryRep = $productCategoryRep;
        $this->productRep = $productRep;
        $this->serviceCategoryRep = $serviceCategoryRep;
        $this->serviceRep = $serviceRep;
        $this->brandProductRep = $brandProductRep;
        $this->brandServiceRep = $brandServiceRep;
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

        $otp           = CommonHelper::createRandomOtp(4);
        $dataCreateOtp = [
            'otp'       => $otp,
            'type'      => TYPE_OTP_FORGOT,
            'user_id'   => $user->id,
            'is_verify' => STATUS_INACTIVE
        ];
        $this->otpRep->create($dataCreateOtp);

        // Put order to queue also
        $job = (new SendOtp($otp, $request['phone']))->onQueue('send_otp');
        dispatch($job);

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
                $errors          = new stdClass();
                $errors->title   = 'Đăng nhập thất bại';
                $errors->message = 'Không tìm thấy tài khoản';
                $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_UNAUTHORIZED);
                return $this->getResponseData();
            }
        } catch (TokenExpiredException $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Đăng nhập thất bại';
            $errors->message = 'Phiên đăng nhập hết hạn';
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_UNAUTHORIZED);
            return $this->getResponseData();
        } catch (TokenInvalidException $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Đăng nhập thất bại';
            $errors->message = 'Phiên đăng nhập không tìm thấy';
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_UNAUTHORIZED);
            return $this->getResponseData();
        } catch (JWTException $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Đăng nhập thất bại';
            $errors->message = 'Không tìm thấy tài khoản';
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_UNAUTHORIZED);
            return $this->getResponseData();
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
            'is_active' => STATUS_INACTIVE,
            'created_at' => date('Y-m-d H:i:s')
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

        // Put order to queue also
        $job = (new SendOtp($otp, $request['phone']))->onQueue('send_otp');
        dispatch($job);

        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Hệ thống đã gửi mã xác thực qua số điện thoại, xin vui lòng nhập ở bước tiếp theo';
        $this->setMessage($msg);
        $this->setData([
            'user_id' => $user->id
        ]);

        // Create default data and base on client default
        $user = $this->userRep->findByAttributes(['phone' => CLIENT_DEFAULT, 'is_delete' => 0, 'is_active' => 1]);
        if (count($user) > 0) {
            $product_category = $this->productCategoryRep->findByAttributes(['client_id' => $user[0]->client_id]);
            $product_category_ids = $product_category->pluck('id');
            $product_category = $product_category->toArray();
            $product = $this->productRep->getByCaterogyIds($product_category_ids, $user[0]->client_id)->toArray();

            $service_category = $this->serviceCategoryRep->findByAttributes(['client_id' => $user[0]->client_id]);
            $service_category_ids = $service_category->pluck('id');
            $service_category = $service_category->toArray();
            $service = $this->serviceRep->getByCaterogyIds($service_category_ids, $user[0]->client_id)->toArray();

            $user = $this->userRep->findByAttributes(['client_id' => $user[0]->client_id])->toArray();;
            unset($user[0]);

            // Product category
            foreach ($product_category as $item) {
                $old_id = $item['id'];
                CommonHelper::removeInformationForDefaultAccount($item, $client->id);
                $category = $this->productCategoryRep->create($item);
                foreach ($product as &$item_product) {
                    if ($item_product['category_id'] === $old_id) {
                        $item_product['category_id'] = $category->id;
                    }
                }
            }

            // Product
            $product_insert = [];
            foreach ($product as $item_product) {
                CommonHelper::removeInformationForDefaultAccount($item_product, $client->id);
                $product_insert[] = $item_product;
            }
            $this->productRep->insertMultiple($product_insert);

            // Brand Product
            $product_after_insert = $this->productRep->findByAttributes(['client_id' => $client->id]);
            $brand_product_insert = [];
            foreach ($product_after_insert as $item) {
                $tmp = [
                    'client_id' => $client->id,
                    'brand_id' => $brand->id,
                    'product_id' => $item->id,
                    'is_active' => STATUS_ACTIVE,
                    'is_delete' => STATUS_INACTIVE,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $brand_product_insert[] = $tmp;
            }
            $this->brandProductRep->insertMultiple($brand_product_insert);

            // Service Category
            foreach ($service_category as $item) {
                $old_id = $item['id'];
                CommonHelper::removeInformationForDefaultAccount($item, $client->id);
                $category = $this->serviceCategoryRep->create($item);
                foreach ($service as &$item_service) {
                    if ($item_service['category_id'] === $old_id) {
                        $item_service['category_id'] = $category->id;
                    }
                }
            }

            // Service
            $service_insert = [];
            foreach ($service as $item) {
                CommonHelper::removeInformationForDefaultAccount($item, $client->id);
                $service_insert[] = $item;
            }
            $this->serviceRep->insertMultiple($service_insert);

            // Brand Service
            $service_after_insert = $this->serviceRep->findByAttributes(['client_id' => $client->id]);
            $brand_service_insert = [];
            foreach ($service_after_insert as $item) {
                $tmp = [
                    'client_id' => $client->id,
                    'brand_id' => $brand->id,
                    'service_id' => $item->id,
                    'is_active' => STATUS_ACTIVE,
                    'is_delete' => STATUS_INACTIVE,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $brand_service_insert[] = $tmp;
            }
            $this->brandServiceRep->insertMultiple($brand_service_insert);

            // User
            $user_insert = [];
            foreach ($user as $item) {
                CommonHelper::removeInformationForDefaultAccount($item, $client->id, $brand->id);
                $user_insert[] = $item;
            }
            $this->userRep->insertMultiple($user_insert);
            // User Role
            $user_after_insert = $this->userRep->findByAttributes(['client_id' => $client->id]);
            unset($user_after_insert[0]);
            $user_role_insert = [];
            foreach ($user_after_insert as $item) {
                $tmp = [
                    'user_id' => $item->id,
                    'role_id' => ROLE_EMPLOYEE,
                    'is_active' => STATUS_ACTIVE,
                    'is_delete' => STATUS_INACTIVE,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $user_role_insert[] = $tmp;
            }
            $this->userRoleRep->insertMultiple($user_role_insert);

        }
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

    public function checkExpired(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $client = $this->clientRep->find($user->client_id);
        // Check correct account
        if (!empty($client)) {
            if (!$client->is_unblock) {
                $now = time();
                $diff = $now - strtotime($client->created_at);
                $day = intval($diff / (3600 * 24));
                if ($day > 30) {
                    $obj = new stdClass();
                    $obj->code = ERRORS['expired_account'];
                    $obj->message = 'Tài khoản của bạn đã bị vô hiệu hoá. Xin vui lòng truy cập lại sau';
                    $this->setMessageDataStatusCode(null, ['errors' => $obj], Response::HTTP_UNAUTHORIZED);
                    return $this->getResponseData();
                }
            }
        }
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Tài khoản đã được kích hoạt';
        $this->setMessage($msg);

        return $this->getResponseData();

    }

    public function clickWarning(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $client = $this->clientRep->find($user->client_id);
        // Check correct account
        if (!empty($client)) {
            $dataUpdate['date_click_warning'] = date('Y-m-d H:i:s');
            $this->clientRep->update($dataUpdate, $client->id);

            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật tài khoản thành công';
            $this->setMessage($msg);
        } else {
            $errors          = new stdClass();
            $errors->title   = 'Thất bại';
            $errors->message = 'Token không khả dụng.';

            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_UNAUTHORIZED);
        }

        return $this->getResponseData();

    }
}
