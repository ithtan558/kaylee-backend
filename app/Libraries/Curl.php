<?php

/**
 * Created by PhpStorm.
 * User: An Huynh
 * Date: 2018/08/28
 * Time: 3:44 PM
 */

namespace App\Libraries;

use App\Models\LogJob;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Curl
{


    /**
     * Call Api
     *
     * @params $orderId
     * @return object order
     */
    public static function callApi($url, $params, $method)
    {
        $orderId = NULL;
        if (isset($params['order_id'])) {
            $orderId = $params['order_id'];
            unset($params['order_id']);
        }
        try {
            $client = new Client(['timeout' => 1000, 'verify' => false]);
            $res    = $client->request(METHOD_POST, $url, ['form_params' => $params]);
            $data   = $res->getBody()->getContents();
            $data   = json_decode($data, true);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $data     = $response->getBody()->getContents();
        }
        $insertLogJobs = array(
            'order_id'     => $orderId,
            'method'       => $method,
            'url'          => $url,
            'params'       => json_encode($params),
            'response'     => json_encode($data),
            'created_date' => date('Y-m-d H:i:s')
        );
        LogJob::insert($insertLogJobs);
    }
}
