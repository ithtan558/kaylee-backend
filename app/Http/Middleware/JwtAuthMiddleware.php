<?php
/*
 * Created by PhpStorm.
 * User: phida
 * Date: 8/17/2016
 * Time: 4:44 PM
 */

namespace App\Middleware;

use App\Libraries\Api;
use Closure;
use Illuminate\Http\Response as IlluminateResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtAuthMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->authenticate($request);

        $userLogin = JWTAuth::parseToken()->authenticate();
        if (!$userLogin) {
            $msg = 'error';
            return Api::response(['message' => $msg], IlluminateResponse::HTTP_UNAUTHORIZED);
        }
        if ($userLogin->sts == STATUS_INACTIVE) {
            $msg = 'error';
            return Api::response(['message' => $msg], IlluminateResponse::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
