<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\UserAdviseRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class UserAdviseService extends BaseService
{
    protected $userAdviseRep;

    public function __construct(
        UserAdviseRepository $userAdviseRep
    )
    {
        $this->userAdviseRep        = $userAdviseRep;
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'name'        => $request['name'],
                'phone'       => $request['phone'],
                'email'    => $request['email'],
                'content'     => $request['content']
            ];

            $this->userAdviseRep->create($dataCreate);

            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Gửi liên hệ thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   =  ' Gửi liên hệ thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

}
