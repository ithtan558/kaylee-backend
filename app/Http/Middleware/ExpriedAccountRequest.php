<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use App\Repositories\ClientRepository;
use Illuminate\Http\Response as IlluminateResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Libraries\Api;
use stdClass;

class ExpriedAccountRequest
{

    protected $userRep;
    protected $clientRep;

    public $attributes;

    public function __construct(UserRepository $userRep, ClientRepository $clientRep)
    {
        $this->userRep = $userRep;
        $this->clientRep = $clientRep;
    }

    public function handle($request, \Closure $next)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $client = $this->clientRep->find($user->client_id);
        $now = time();
        $diff = $now - strtotime($client->created_at);
        $day = intval($diff/(3600*24));
        if (!$client->is_unblock) {
            if ($day > 30) {
                $errors = [];
                $obj = new stdClass();
                $obj->code = ERRORS['expired_account'];
                $obj->message = 'Tài khoản của bạn đã bị vô hiệu hoá. Xin vui lòng truy cập lại sau';
                $errors[] = $obj;

                return Api::response(['message' => '', 'data' => ['errors' => $errors]], IlluminateResponse::HTTP_UNAUTHORIZED);
            } elseif (!$client->date_click_warning != null && (25 <= $day && $day <= 30)) {
                $obj = new stdClass();
                $obj->code = WARNING['warning_expired'];
                $obj->message = 'Bạn đang sử dụng tài khoản thử nghiệm, hạn sử dụng còn lại ' . (30 - $day) . ' ngày. Xin vui lòng liên hệ với ban quản trị để biết thêm chi tiết';
                $data[] = $obj;
                $request->attributes->add(['data_warning' => $data]);
            }
        }
        return $next($request);
    }

}
