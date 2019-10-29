<?php

namespace App\Libraries;

use Illuminate\Http\Response;

class Api
{
    public static function response($result = null, $statusCode = Response::HTTP_OK)
    {
        $dataReturn = [
            'status'  => $statusCode == Response::HTTP_OK ? RESPONSE_SUCCESS : RESPONSE_FAILED,
            'message' => isset($result['message']) ? $result['message'] : '',
            'data'    => isset($result['data']) ? $result['data'] : '',
        ];

        return response()->json($dataReturn, $statusCode);
    }
}
