<?php

namespace App\Http\Middleware;

use App\Repositories\VersionRepository;
use Illuminate\Http\Response as IlluminateResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Libraries\Api;
use stdClass;

class VersionRequest
{

    protected $versionRep;

    public $attributes;

    public function __construct(VersionRepository $versionRep)
    {
        $this->versionRep = $versionRep;
    }

    public function handle($request, \Closure $next)
    {

        $version_code = $request->header('version');
        if (!empty($version_code)) {
            $version = $this->versionRep->checkVersion($version_code);
            if (!empty($version->code)) {
                $obj = new stdClass();
                $obj->code = VERSION['new_version'];
                $obj->message = $version->description;
                $errors[] = $obj;

                return Api::response(['message' => '', 'data' => ['errors' => $errors]], IlluminateResponse::HTTP_BAD_REQUEST);

            }
        } else {
            $obj = new stdClass();
            $obj->code = VERSION['not_found_version'];
            $obj->message = "Không tìm thấy Version code";
            $errors[] = $obj;
            return Api::response(['message' => '', 'data' => ['errors' => $errors]], IlluminateResponse::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }

}
