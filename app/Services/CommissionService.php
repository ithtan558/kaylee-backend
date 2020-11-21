<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\OrderRepository;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\UserConfigCommissionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class CommissionService extends BaseService
{
    protected $orderRep;
    protected $orderDetailRep;
    protected $orderPaymentRep;
    protected $customerRep;
    protected $userConfigCommissionRep;

    public function __construct(
        OrderRepository $orderRep,
        OrderDetailRepository $orderDetailRep,
        OrderPaymentRepository $orderPaymentRep,
        CustomerRepository $customerRep,
        UserConfigCommissionRepository $userConfigCommissionRep
    )
    {
        $this->orderRep                = $orderRep;
        $this->orderDetailRep          = $orderDetailRep;
        $this->orderPaymentRep         = $orderPaymentRep;
        $this->customerRep             = $customerRep;
        $this->userConfigCommissionRep = $userConfigCommissionRep;
    }

    public function detail(Request $request)
    {
        $orders                 = $this->orderRep->getTotalCommission($request->all(), false);
        $user_config_commission = $this->userConfigCommissionRep->findByAUserId(['user_id' => $request['user_id']]);
        $obj                    = new stdClass();
        $obj->total             = 0;
        $obj->commission        = 0;
        $data                   = [
            'commission_total'   => 0,
            "commission_product" => $obj,
            "commission_service" => $obj
        ];
        if (!empty($user_config_commission) > 0) {

            $commission_product             = new \stdClass();
            $commission_product->total      = 0;
            $commission_product->commission = 0;

            $commission_service             = new \stdClass();
            $commission_service->total      = 0;
            $commission_service->commission = 0;
            foreach ($orders as $item) {
                foreach ($item->order_details as $order_detail) {
                    if (!empty($order_detail->product_id)) {
                        $commission_product->total      += $order_detail->total;
                        $commission_product->commission += ($user_config_commission->commission_product / 100) * $order_detail->total;
                    } else {
                        $commission_service->total      += $order_detail->total;
                        $commission_service->commission += ($user_config_commission->commission_service / 100) * $order_detail->total;
                    }
                }
            }
            $commission_total = $commission_product->commission + $commission_service->commission;
            $data             = [
                'commission_total'   => $commission_total,
                'commission_product' => $commission_product,
                'commission_service' => $commission_service,
            ];
        }

        $this->setData($data);

        return $this->getResponseData();
    }

    public function getList(Request $request, $commission_product = false, $commission_service = false)
    {
        $data = $this->orderRep->getList($request->all());

        // Calculation for commission

        $user_config_commission = $this->userConfigCommissionRep->findByAUserId(['user_id' => $request['user_id']]);
        if ($commission_product) {
            foreach ($data['items'] as $index => &$item) {
                $item->commission_product = 0;
                foreach ($item->order_details as $order_detail) {
                    $item->commission_product += !empty($user_config_commission) && !empty($order_detail->product_id) ? ($user_config_commission->commission_product / 100) * $order_detail->total : 0;
                }
                $item->date = date('d/m h:i:s', strtotime($item->created_at));
                unset($item->order_details);
                if ($item->commission_product == 0) {
                    //unset($data['items'][$index]);
                }
            }
        }
        if ($commission_service) {
            foreach ($data['items'] as $index => &$item) {
                $item->commission_service = 0;
                foreach ($item->order_details as $order_detail) {
                    $item->commission_service += !empty($user_config_commission) && !empty($order_detail->service_id) ? ($user_config_commission->commission_service / 100) * $order_detail->total : 0;
                }

                $item->date = date('d/m h:i:s', strtotime($item->created_at));
                unset($item->order_details);
                if ($item->commission_service == 0) {
                    //unset($data['items'][$index]);
                }
            }
        }
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetailSetting(Request $request)
    {
        $data                     = new stdClass();
        $data->commission_product = 0;
        $data->commission_service = 0;
        $data_query               = $this->userConfigCommissionRep->getByUserId($request['user_id']);
        if (!empty($data_query)) {
            $data = $this->userConfigCommissionRep->getByUserId($request['user_id']);
        }

        $this->setData($data);

        return $this->getResponseData();
    }

    public function updateSetting(Request $request)
    {
        try {
            $user_config_query = $this->userConfigCommissionRep->findByAttributes(['user_id' => $request['user_id']]);
            if (count($user_config_query) > 0) {
                $dataUpdate = [
                    'commission_product' => $request['commission_product'],
                    'commission_service'  => $request['commission_service'],
                    'updated_by'          => $this->getCurrentUser('id')
                ];

                $this->userConfigCommissionRep->updateByMultipleCondition($dataUpdate, ['user_id' => $request['user_id']]);
            } else {
                $dataInsert = [
                    'user_id'             => $request['user_id'],
                    'client_id'           => $this->getCurrentUser('client_id'),
                    'commission_product' => $request['commission_product'],
                    'commission_service'  => $request['commission_service'],
                    'created_by'          => $this->getCurrentUser('id')
                ];

                $this->userConfigCommissionRep->create($dataInsert);
            }

            $msg          = new stdClass();
            $msg->title   = 'Thành công';
            $msg->content = 'Cập nhật cài đặt hoa hồng cho nhân viên thành công';
            $this->setMessage($msg);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Cập nhật cài đặt hoa hồng cho nhân viên thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

}
