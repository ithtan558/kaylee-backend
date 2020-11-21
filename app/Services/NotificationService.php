<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class NotificationService extends BaseService
{
    protected $notificationRep;

    public function __construct(NotificationRepository $notificationRep)
    {
        $this->notificationRep = $notificationRep;
    }

    public function getList(Request $request)
    {
        $data = $this->notificationRep->getList($request->all());
        foreach ($data['items'] as &$item) {
            $item->date = date('d/m', strtotime($item->created_at));
        }
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data        = $this->notificationRep->getDetail($id);
        $data->image = PATH_IMAGE . $data->image;
        $this->setCityDistrictWards($data);

        $this->setData($data);

        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->notificationRep->update(["is_delete" => 1], $id);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa thông báo thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

    public function deleteAll()
    {
        $this->notificationRep->updateByMultipleCondition(["is_delete" => 1], ['client_id' => $this->getCurrentUser('client_id')]);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa tất cả thông báo thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

    public function getCount()
    {
        $notification = $this->notificationRep->countUnRead();
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Lấy tổng thông báo chưa đọc thành công';
        $this->setMessage($msg);
        $this->setData(['count' => $notification]);

        return $this->getResponseData();
    }

    public function updateStatus($params)
    {
        $this->notificationRep->update(["status" => $params['status']], $params['id']);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Cập nhật thông báo thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
