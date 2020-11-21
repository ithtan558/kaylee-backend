<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\ProductCategoryRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class ProductCategoryService extends BaseService
{
    protected $productCategoryRep;

    public function __construct(ProductCategoryRepository $productCategoryRep)
    {
        $this->productCategoryRep = $productCategoryRep;
    }

    public function getAll(Request $request)
    {
        $data = $this->productCategoryRep->getAll($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request)
    {
        $data = $this->productCategoryRep->getList($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data = $this->productCategoryRep->getDetail($id);
        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'  => $this->getCurrentUser('client_id'),
                'name'       => $request['name'],
                'code'       => $request['code'],
                'sequence'   => $request['sequence'],
                'is_active'  => STATUS_ACTIVE,
                'created_by' => $this->getCurrentUser('id')
            ];

            $this->productCategoryRep->create($dataCreate);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Tạo loại sản phẩm thành công';
            $this->setMessage($msg);
            $this->setData($dataCreate);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo loại sản phẩm thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function update(Request $request)
    {
        try {
            $dataUpdate = [
                'name'       => $request['name'],
                'code'       => $request['code'],
                'sequence'   => $request['sequence'],
                'is_active'  => STATUS_ACTIVE,
                'updated_by' => $this->getCurrentUser('id')
            ];

            $this->productCategoryRep->update($dataUpdate, $request['id']);
            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật loại sản phẩm thành công';
            $this->setMessage($msg);
            $this->setData($dataUpdate);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo loại sản phẩm thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->productCategoryRep->update(['is_delete' => 1], $id);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa loại sản phẩm thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
