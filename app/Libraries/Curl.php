<?php

/**
 * Created by PhpStorm.
 * User: An Huynh
 * Date: 2018/08/28
 * Time: 3:44 PM
 */

namespace App\Libraries;

use App\Models\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Curl
{


    /**
     * Call Api
     *
     * @params $url, $params
     * @return null
     */
    public static function callApi($url, $params)
    {

        try {
            $client = new Client(['timeout' => 1000, 'verify' => false]);
            $res    = $client->request('POST', $url, ['form_params' => $params]);
            $data   = $res->getBody()->getContents();
            $data   = json_decode($data, true);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $data     = $response->getBody()->getContents();
        }
        $insertLog = array(
            'url'          => $url,
            'request'       => json_encode($params),
            'response'     => json_encode($data),
            'created_at' => date('Y-m-d H:i:s')
        );
        Log::insert($insertLog);
    }
}
