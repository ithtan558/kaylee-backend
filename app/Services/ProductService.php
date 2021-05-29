<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\ProductRepository;
use App\Repositories\BrandProductRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class ProductService extends BaseService
{
    protected $productRep;
    protected $brandProductRep;

    public function __construct(
        ProductRepository $productRep,
        BrandProductRepository $brandProductRep
    )
    {
        $this->productRep      = $productRep;
        $this->brandProductRep = $brandProductRep;
    }

    public function getAll()
    {
        $data = $this->productRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request)
    {
        $data = $this->productRep->getList($request->all());
        foreach ($data['items'] as &$item) {
            if (!empty($item->image)) {
                if (!empty($item->supplier_id)) {
                    $item->image = PATH_IMAGE_SUPPLIER . $item->image;
                } else {
                    $item->image = PATH_IMAGE . $item->image;
                }
            }
        }
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($id)
    {
        $data          = $this->productRep->getDetail($id);
        $brandProducts = $this->brandProductRep->getByProductId($id);
        $brands        = [];
        foreach ($brandProducts as $brandProduct) {
            $obj       = new stdClass();
            $obj->id   = $brandProduct->brand_id;
            $obj->name = $brandProduct->brand_name;
            $brands[]  = $obj;
        }
        $data->brands = $brands;

        // Category
        $obj            = new stdClass();
        $obj->id        = $data->category_id;
        $obj->name      = $data->category_name;
        $data->category = $obj;

        unset($data->category_id);
        unset($data->category_name);

        $images = [];
        if (!empty($data->supplier_id)) {

            if (!empty($data->video)) {
                $obj = new stdClass();
                $obj->type = 2;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->video;
                $images[] = $obj;
            }

            if (!empty($data->video1)) {
                $obj = new stdClass();
                $obj->type = 2;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->video1;
                $images[] = $obj;
            }

            if (!empty($data->video2)) {
                $obj = new stdClass();
                $obj->type = 2;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->video2;
                $images[] = $obj;
            }

            if (!empty($data->video3)) {
                $obj = new stdClass();
                $obj->type = 2;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->video3;
                $images[] = $obj;
            }

            if (!empty($data->video4)) {
                $obj = new stdClass();
                $obj->type = 2;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->video4;
                $images[] = $obj;
            }

            if (!empty($data->image)) {
                $obj = new stdClass();
                $obj->type = 1;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->image;
                $images[] = $obj;
            }
            if (!empty($data->image1)) {
                $obj = new stdClass();
                $obj->type = 1;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->image1;
                $images[] = $obj;
            }
            if (!empty($data->image2)) {
                $obj = new stdClass();
                $obj->type = 1;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->image2;
                $images[] = $obj;
            }
            if (!empty($data->image3)) {
                $obj = new stdClass();
                $obj->type = 1;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->image3;
                $images[] = $obj;
            }
            if (!empty($data->image4)) {
                $obj = new stdClass();
                $obj->type = 1;
                $obj->value = PATH_IMAGE_SUPPLIER.$data->image4;
                $images[] = $obj;
            }

            $data->images = $images;

            unset($data->image);
        } else {
            $data->image = PATH_IMAGE.$data->image;
        }
        unset($data->image1);
        unset($data->image2);
        unset($data->image3);
        unset($data->image4);
        unset($data->video);

        $data->description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $data->description);
        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $dataCreate = [
                'client_id'   => $this->getCurrentUser('client_id'),
                'name'        => $request['name'],
                'code'        => $request['code'],
                'description' => $request['description'],
                'category_id' => $request['category_id'],
                'price'       => $request['price'],
                'is_active'   => STATUS_ACTIVE,
                'created_by'  => $this->getCurrentUser('id')
            ];

            $name                = CommonHelper::uploadImage($request);
            $dataCreate['image'] = $name;

            $product = $this->productRep->create($dataCreate);

            // Insert brand product table
            $arr_brand = explode(',', $request['brand_ids']);
            foreach ($arr_brand as $brand) {
                $dataCreateBrandProduct = [
                    'brand_id'   => $brand,
                    'product_id' => $product->id,
                    'is_active'   => STATUS_ACTIVE,
                ];
                $this->brandProductRep->create($dataCreateBrandProduct);
            }

            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Tạo sản phẩm thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo sản phẩm thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function update(Request $request)
    {
        try {
            $dataUpdate = [
                'name'        => $request['name'],
                'code'        => $request['code'],
                'description' => $request['description'],
                'category_id' => $request['category_id'],
                'price'       => $request['price'],
                'is_active'   => STATUS_ACTIVE,
                'updated_by'  => $this->getCurrentUser('id')
            ];

            if ($request->hasFile('image')) {
                $name                = CommonHelper::uploadImage($request);
                $dataUpdate['image'] = $name;
            }

            $this->productRep->update($dataUpdate, $request['id']);

            // Delete all brand product of this product first

            $this->brandProductRep->deleteByProductId($request['id']);
            // Insert brand product table
            $arr_brand = explode(',', $request['brand_ids']);
            foreach ($arr_brand as $brand) {
                $dataCreateBrandProduct = [
                    'brand_id'   => $brand,
                    'product_id' => $request['id']
                ];
                $this->brandProductRep->create($dataCreateBrandProduct);
            }

            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật sản phẩm thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Cập nhật sản phẩm thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function delete($id)
    {
        $this->productRep->update(['is_delete' => 1], $id);
        $this->brandProductRep->updateByMultipleCondition(['is_delete' => 1], ['product_id' => $id]);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Xóa sản phẩm thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
