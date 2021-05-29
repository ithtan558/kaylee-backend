<?php

namespace App\Libraries;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Api
{
    public static function response($result = null, $statusCode = Response::HTTP_OK, $request = null)
    {
        $dataReturn = [
            'status'  => $statusCode == Response::HTTP_OK ? RESPONSE_SUCCESS : RESPONSE_FAILED,
            'message' => isset($result['message']) ? $result['message'] : null
        ];
        if ($statusCode == Response::HTTP_OK) {
            $dataReturn['data'] = isset($result['data']) ? $result['data'] : null;
        } else {
            $dataReturn['errors'] = isset($result['data']['errors']) ? $result['data']['errors'] : null;
        }

        if ($request != null && !empty($request->get('data_warning'))) {
            $dataReturn['data_warning'] = $request->get('data_warning');
        }

        return response()->json($dataReturn, $statusCode);
    }
}
