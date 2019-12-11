<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\OrderRepository;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderService extends BaseService
{
    protected $orderRep;
    protected $orderDetailRep;
    protected $orderPaymentRep;
    protected $customerRep;

    public function __construct(
        OrderRepository $orderRep,
        OrderDetailRepository $orderDetailRep,
        OrderPaymentRepository $orderPaymentRep,
        CustomerRepository $customerRep
    )
    {
        $this->orderRep        = $orderRep;
        $this->orderDetailRep  = $orderDetailRep;
        $this->orderPaymentRep = $orderPaymentRep;
        $this->customerRep     = $customerRep;
    }

    public function getList(Request $request)
    {
        $data = $this->orderRep->getList($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $items    = $request['cart_items'];
            $customer = $request['cart_customer'];
            $discount = $request['cart_discount'];
            $employee = $request['cart_employee'];
            // Check exist customer of not
            if (!empty($customer)) {
                if (isset($customer['id'])) {
                    $customer_id = $customer['id'];
                } else {
                    $dataInsertCustomer = [
                        'client_id' => $this->getCurrentUser('client_id'),
                        'name'      => $customer['name'],
                        'phone'     => $customer['phone'],
                        /*'email'     => $customer['email'],*/
                        'type_id'   => CUSTOMER_NORMAL
                    ];
                    $customer           = $this->customerRep->create($dataInsertCustomer);
                    $customer_id        = $customer['id'];
                }
            } else {
                $customer_id = 0;
                $customer    = [
                    'name'  => 'Khách lẻ',
                    'phone' => '',
                    'email' => ''
                ];
            }

            // Insert order
            $dataInsertOrder = [
                'client_id'       => $this->getCurrentUser('client_id'),
                'brand_id'        => $this->getCurrentUser('brand_id'),
                'employee_id'     => $employee['id'],
                'customer_id'     => $customer_id,
                'order_status_id' => ORDER_STATUS_FINISHED,
                'is_paid'         => 1,
                'name'            => $customer['name'],
                'phone'           => $customer['phone'],
                'email'           => $customer['email'],
                'note'            => '',
                'amount'          => 0,
                'discount'        => 0,
                'created_by'      => $this->getCurrentUser('id')
            ];
            $order           = $this->orderRep->create($dataInsertOrder);

            // Insert order detail
            $amount = 0;
            foreach ($items as $item) {
                $total                 = $item['price'] * $item['qty'];
                $dataInsertOrderDetail = [
                    'order_id'   => $order['id'],
                    'service_id' => $item['id'],
                    'name'       => $item['name'],
                    'price'      => $item['price'],
                    'quantity'   => $item['qty'],
                    'total'      => $total,
                    'note'
                ];
                $this->orderDetailRep->create($dataInsertOrderDetail);
                $amount += $total;
            }

            // Insert order payment
            $dataInsertOrderPayment = [
                'order_id'          => $order['id'],
                'payment_method_id' => PAYMENT_METHOD_CASH,
                'value'             => $amount,
                'change'            => 0,
                'total_payment'     => $amount
            ];
            $this->orderPaymentRep->create($dataInsertOrderPayment);

            // Update amount for order
            $this->orderRep->update([
                'amount'   => $amount - (($amount * $discount) / 100),
                'discount' => ($amount * $discount) / 100,
                'code'     => CommonHelper::createRandomPassword($order['id'])
            ],
                $order['id']
            );

            $this->setMessage('Tạo đơn hàng thành công');
            $this->setData($dataInsertOrder);
        } catch (\Exception $ex) {
            $this->setMessage($ex->getMessage());
            $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function getAll()
    {
        $data = $this->orderRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getCount()
    {
        $data = $this->orderRep->getCount();
        $this->setData($data);

        return $this->getResponseData();
    }

}
