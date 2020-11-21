<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\OrderRepository;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\UserLocationRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ServiceRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use stdClass;

class OrderService extends BaseService
{
    protected $orderRep;
    protected $orderDetailRep;
    protected $orderPaymentRep;
    protected $customerRep;
    protected $userLocationRep;
    protected $productRep;
    protected $serviceRep;

    public function __construct(
        OrderRepository $orderRep,
        OrderDetailRepository $orderDetailRep,
        OrderPaymentRepository $orderPaymentRep,
        CustomerRepository $customerRep,
        UserLocationRepository $userLocationRep,
        ProductRepository $productRep,
        ServiceRepository $serviceRep
    )
    {
        $this->orderRep        = $orderRep;
        $this->orderDetailRep  = $orderDetailRep;
        $this->orderPaymentRep = $orderPaymentRep;
        $this->customerRep     = $customerRep;
        $this->userLocationRep = $userLocationRep;
        $this->productRep      = $productRep;
        $this->serviceRep      = $serviceRep;
    }

    public function getList(Request $request)
    {
        $data      = $this->orderRep->getList($request->all());
        $order_ids = [];
        foreach ($data['items'] as &$item) {
            if (isset($request['is_history_by_supplier'])) {
                $item->count = count($item->order_details);
            }
            unset($item->order_details);
        }

        /*$data_order_detail = $this->orderRep->getListOrderDetailByOrderIds($order_ids);
        foreach ($data as &$item) {
            $item->order_details = [];

        }*/
        $this->setData($data);

        return $this->getResponseData();
    }

    public function create(Request $request)
    {
        try {
            $items                = $request['cart_items'];
            $customer             = $request['cart_customer'];
            $discount             = $request['cart_discount'];
            $employee             = $request['cart_employee'];
            $supplier_information = $request['cart_supplier_information'];
            // Check exist customer of not
            if (!empty($customer)) {
                if (isset($customer['id'])) {
                    $customer_id = $customer['id'];
                } else {
                    // Check exist customer before by phone
                    $customer_query = $this->customerRep->findByAttributes(['phone' => $customer['phone']]);
                    if (count($customer_query) > 0) {
                        $customer_id = $customer_query[0]->id;
                    } else {
                        // Check if phone = null -> auto create 1 number phone for them by generate number phone
                        if (empty($customer['phone'])) {
                            $phone = NUMBER_PREFIXES[array_rand(NUMBER_PREFIXES)].CommonHelper::randomNumberSequence();
                        } else {
                            $phone = $customer['phone'];
                        }
                        $dataInsertCustomer = [
                            'client_id'        => $this->getCurrentUser('client_id'),
                            'first_name'       => isset($customer['first_name']) ? $customer['first_name']: '',
                            'last_name'        => $customer['last_name'],
                            'hometown_city_id' => isset($customer['hometown_city_id']) ? $customer['hometown_city_id'] : 0,
                            'city_id'          => isset($customer['city_id']) ? $customer['city_id'] : 0,
                            'district_id'      => isset($customer['district_id']) ? $customer['district_id'] : 0,
                            'wards_id'         => isset($customer['wards_id']) ? $customer['wards_id'] : 0,
                            'phone'            => $phone,
                            'email'            => isset($customer['email']) ? $customer['email'] : '',
                            'type_id'          => CUSTOMER_NORMAL
                        ];
                        $customer           = $this->customerRep->create($dataInsertCustomer);
                        $customer_id        = $customer['id'];
                    }
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
            // Set status for order
            $order_status_id = ORDER_STATUS_NOT_PAID;
            $is_paid = 0;
            if (isset($request['supplier_id'])) {
                $order_status_id = ORDER_STATUS_WAITING;
                $is_paid = 1;
            } elseif (isset($request['is_paid'])) {
                $order_status_id = ORDER_STATUS_FINISHED;
                $is_paid = 1;
            }
            $dataInsertOrder = [
                'client_id'       => $this->getCurrentUser('client_id'),
                'brand_id'        => isset($request['brand_id']) ? $request['brand_id'] : $this->getCurrentUser('brand_id'),
                'employee_id'     => isset($request['supplier_id']) ? $this->getCurrentUser('id') : $employee,
                'customer_id'     => isset($request['supplier_id']) ? $this->getCurrentUser('id') : $customer_id,
                'order_status_id' => $order_status_id,
                'is_paid'         => $is_paid,
                'supplier_id'     => isset($request['supplier_id']) ? $request['supplier_id'] : 0,
                'name'            => isset($request['supplier_id']) ? $supplier_information['name'] : $customer['last_name'] . ' ' . $customer['first_name'],
                'phone'           => isset($request['supplier_id']) ? $supplier_information['phone'] : $customer['phone'],
                'note'            => isset($request['supplier_id']) ? $supplier_information['note'] : '',
                'amount'          => 0,
                'discount'        => 0,
                'created_by'      => $this->getCurrentUser('id')
            ];
            $order           = $this->orderRep->create($dataInsertOrder);

            // Insert user location
            if (!empty($request['supplier_id'])) {
                $dataInsertUserLocation = [
                    'client_id'   => $this->getCurrentUser('client_id'),
                    'order_id'    => $order->id,
                    'name'        => $supplier_information['name'],
                    'address'     => $supplier_information['address'],
                    'wards_id'    => $supplier_information['wards_id'],
                    'district_id' => $supplier_information['district_id'],
                    'city_id'     => $supplier_information['city_id'],
                    'note'        => $supplier_information['note']
                ];
                $this->userLocationRep->create($dataInsertUserLocation);
            }

            // Insert order detail
            $amount      = 0;
            $service_ids = [];
            $product_ids = [];
            foreach ($items as $item) {
                if (isset($item['service_id'])) {
                    $service_ids[] = $item['service_id'];
                }
                if (isset($item['product_id'])) {
                    $product_ids[] = $item['product_id'];
                }
            }

            $products = $this->productRep->findByMany($product_ids)->keyBy('id');
            $services = $this->serviceRep->findByMany($service_ids)->keyBy('id');

            foreach ($items as $item) {
                $flag       = false;
                $product_id = 0;
                $service_id = 0;
                $total      = 0;
                $name       = '';
                $price      = 0;
                if (isset($item['service_id']) && isset($services[$item['service_id']])) {
                    $flag       = true;
                    $service_id = $item['service_id'];
                    $total      = $services[$service_id]->price * $item['quantity'];
                    $name       = $services[$service_id]->name;
                    $price      = $services[$service_id]->price;
                }
                if (isset($item['product_id']) && isset($products[$item['product_id']])) {
                    $flag       = true;
                    $product_id = $item['product_id'];
                    $total      = $products[$product_id]->price * $item['quantity'];
                    $name       = $products[$product_id]->name;
                    $price      = $products[$product_id]->price;
                }

                if ($flag) {
                    $dataInsertOrderDetail = [
                        'order_id'   => $order['id'],
                        'service_id' => $service_id,
                        'product_id' => $product_id,
                        'name'       => $name,
                        'price'      => $price,
                        'quantity'   => $item['quantity'],
                        'total'      => $total
                    ];
                    $this->orderDetailRep->create($dataInsertOrderDetail);
                    $amount += $total;
                }
            }

            // Insert order payment
            if ($is_paid) {
                $dataInsertOrderPayment = [
                    'order_id'          => $order['id'],
                    'payment_method_id' => PAYMENT_METHOD_CASH,
                    'value'             => $amount,
                    'change'            => 0,
                    'total_payment'     => $amount
                ];
                $this->orderPaymentRep->create($dataInsertOrderPayment);
            }

            // Update amount for order
            $this->orderRep->update([
                'amount'   => $amount - (($amount * $discount) / 100),
                'discount' => ($amount * $discount) / 100,
                'code'     => CommonHelper::createRandomCode()
            ],
                $order['id']
            );

            $msg        = new stdClass();
            $msg->title = 'Đặt hàng Thành công';
            if (!empty($request['supplier_id'])) {
                $msg->content = 'Kiểm tra tình trạng đơn đặt hàng tại Tài khoản > Quản lý đơn đặt hàng';
            } else {
                $msg->content = 'Kiểm tra tình trạng đơn đặt hàng tại trang chủ > Thu ngân';
            }
            $this->setMessage($msg);
            $this->setData($dataInsertOrder);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo đơn hàng thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function createSupplier(Request $request)
    {
        try {
            $items                = $request['cart_items'];
            $customer             = $request['cart_customer'];
            $discount             = $request['cart_discount'];
            $employee             = $request['cart_employee'];
            $supplier_information = $request['cart_supplier_information'];
            // Check exist customer of not
            if (!empty($customer)) {
                if (isset($customer['id'])) {
                    $customer_id = $customer['id'];
                } else {
                    // Check exist customer before by phone
                    $customer_query = $this->customerRep->findByAttributes(['phone' => $customer['phone']]);
                    if (count($customer_query) > 0) {
                        $customer_id = $customer_query[0]->id;
                    } else {
                        $dataInsertCustomer = [
                            'client_id'        => $this->getCurrentUser('client_id'),
                            'first_name'       => $customer['first_name'],
                            'last_name'        => $customer['last_name'],
                            'hometown_city_id' => isset($customer['hometown_city_id']) ? $customer['hometown_city_id'] : 0,
                            'city_id'          => isset($customer['city_id']) ? $customer['city_id'] : 0,
                            'district_id'      => isset($customer['district_id']) ? $customer['district_id'] : 0,
                            'wards_id'         => isset($customer['wards_id']) ? $customer['wards_id'] : 0,
                            'phone'            => isset($customer['phone']) ? $customer['phone'] : '',
                            'email'            => isset($customer['email']) ? $customer['email'] : '',
                            'type_id'          => CUSTOMER_NORMAL
                        ];
                        $customer           = $this->customerRep->create($dataInsertCustomer);
                        $customer_id        = $customer['id'];
                    }
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
            // Set status for order
            $order_status_id = ORDER_STATUS_NOT_PAID;
            $is_paid = 0;
            if (isset($request['supplier_id'])) {
                $order_status_id = ORDER_STATUS_WAITING;
                $is_paid = 1;
            } elseif (isset($request['is_paid'])) {
                $order_status_id = ORDER_STATUS_FINISHED;
                $is_paid = 1;
            }
            $dataInsertOrder = [
                'client_id'       => $this->getCurrentUser('client_id'),
                'brand_id'        => isset($request['brand_id']) ? $request['brand_id'] : $this->getCurrentUser('brand_id'),
                'employee_id'     => isset($request['supplier_id']) ? $this->getCurrentUser('id') : $employee,
                'customer_id'     => isset($request['supplier_id']) ? $this->getCurrentUser('id') : $customer_id,
                'order_status_id' => $order_status_id,
                'is_paid'         => $is_paid,
                'supplier_id'     => isset($request['supplier_id']) ? $request['supplier_id'] : 0,
                'name'            => isset($request['supplier_id']) ? $supplier_information['name'] : $customer['last_name'] . ' ' . $customer['first_name'],
                'phone'           => isset($request['supplier_id']) ? $supplier_information['phone'] : $customer['phone'],
                'note'            => isset($request['supplier_id']) ? $supplier_information['note'] : '',
                'amount'          => 0,
                'discount'        => 0,
                'created_by'      => $this->getCurrentUser('id')
            ];
            $order           = $this->orderRep->create($dataInsertOrder);

            // Insert user location
            if (!empty($request['supplier_id'])) {
                $dataInsertUserLocation = [
                    'client_id'   => $this->getCurrentUser('client_id'),
                    'order_id'    => $order->id,
                    'name'        => $supplier_information['name'],
                    'address'     => $supplier_information['address'],
                    'wards_id'    => $supplier_information['wards_id'],
                    'district_id' => $supplier_information['district_id'],
                    'city_id'     => $supplier_information['city_id'],
                    'note'        => $supplier_information['note']
                ];
                $this->userLocationRep->create($dataInsertUserLocation);
            }

            // Insert order detail
            $amount      = 0;
            $service_ids = [];
            $product_ids = [];
            foreach ($items as $item) {
                if (isset($item['service_id'])) {
                    $service_ids[] = $item['service_id'];
                }
                if (isset($item['product_id'])) {
                    $product_ids[] = $item['product_id'];
                }
            }

            $products = $this->productRep->findByMany($product_ids)->keyBy('id');
            $services = $this->serviceRep->findByMany($service_ids)->keyBy('id');

            foreach ($items as $item) {
                $flag       = false;
                $product_id = 0;
                $service_id = 0;
                $total      = 0;
                $name       = '';
                $price      = 0;
                if (isset($item['service_id']) && isset($services[$item['service_id']])) {
                    $flag       = true;
                    $service_id = $item['service_id'];
                    $total      = $services[$service_id]->price * $item['quantity'];
                    $name       = $services[$service_id]->name;
                    $price      = $services[$service_id]->price;
                }
                if (isset($item['product_id']) && isset($products[$item['product_id']])) {
                    $flag       = true;
                    $product_id = $item['product_id'];
                    $total      = $products[$product_id]->price * $item['quantity'];
                    $name       = $products[$product_id]->name;
                    $price      = $products[$product_id]->price;
                }

                if ($flag) {
                    $dataInsertOrderDetail = [
                        'order_id'   => $order['id'],
                        'service_id' => $service_id,
                        'product_id' => $product_id,
                        'name'       => $name,
                        'price'      => $price,
                        'quantity'   => $item['quantity'],
                        'total'      => $total
                    ];
                    $this->orderDetailRep->create($dataInsertOrderDetail);
                    $amount += $total;
                }
            }

            // Insert order payment
            if ($is_paid) {
                $dataInsertOrderPayment = [
                    'order_id'          => $order['id'],
                    'payment_method_id' => PAYMENT_METHOD_CASH,
                    'value'             => $amount,
                    'change'            => 0,
                    'total_payment'     => $amount
                ];
                $this->orderPaymentRep->create($dataInsertOrderPayment);
            }

            // Update amount for order
            $this->orderRep->update([
                'amount'   => $amount - (($amount * $discount) / 100),
                'discount' => ($amount * $discount) / 100,
                'code'     => CommonHelper::createRandomCode($order['id'])
            ],
                $order['id']
            );

            $msg        = new stdClass();
            $msg->title = 'Đặt hàng Thành công';
            if (!empty($request['supplier_id'])) {
                $msg->content = 'Kiểm tra tình trạng đơn đặt hàng tại Tài khoản > Quản lý đơn đặt hàng';
            } else {
                $msg->content = 'Kiểm tra tình trạng đơn đặt hàng tại trang chủ > Thu ngân';
            }
            $this->setMessage($msg);
            $this->setData($dataInsertOrder);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Tạo đơn hàng thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function update(Request $request)
    {
        try {
            $items                = $request['cart_items'];
            $customer             = $request['cart_customer'];
            $discount             = $request['cart_discount'];
            $employee             = $request['cart_employee'];
            $supplier_information = $request['cart_supplier_information'];
            $id                   = $request['id'];

            // Delete all old information with current order
            $this->orderRep->delete(['id' => $id]);
            $this->orderDetailRep->delete(['order_id' => $id]);
            $this->orderDetailRep->delete(['order_id' => $id]);
            if (!empty($request['supplier_id'])) {
                $this->userLocationRep->delete(['order_id' => $id]);
            }

            // Check exist customer of not
            if (!empty($customer)) {
                if (isset($customer['id'])) {
                    $customer_id = $customer['id'];
                } else {
                    // Check exist customer before by phone
                    $customer_query = $this->customerRep->findByAttributes(['phone' => $customer['phone']]);
                    if (count($customer_query) > 0) {
                        $customer_id = $customer_query[0]->id;
                    } else {
                        $dataInsertCustomer = [
                            'client_id'        => $this->getCurrentUser('client_id'),
                            'first_name'       => $customer['first_name'],
                            'last_name'        => $customer['last_name'],
                            'hometown_city_id' => isset($customer['hometown_city_id']) ? $customer['hometown_city_id'] : 0,
                            'city_id'          => isset($customer['city_id']) ? $customer['city_id'] : 0,
                            'district_id'      => isset($customer['district_id']) ? $customer['district_id'] : 0,
                            'wards_id'         => isset($customer['wards_id']) ? $customer['wards_id'] : 0,
                            'phone'            => isset($customer['phone']) ? $customer['phone'] : '',
                            'email'            => isset($customer['email']) ? $customer['email'] : '',
                            'type_id'          => CUSTOMER_NORMAL
                        ];
                        $customer           = $this->customerRep->create($dataInsertCustomer);
                        $customer_id        = $customer['id'];
                    }
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
            // Set status for order
            $order_status_id = ORDER_STATUS_NOT_PAID;
            $is_paid = 0;
            if (isset($request['is_paid'])) {
                $order_status_id = ORDER_STATUS_FINISHED;
                $is_paid = 1;
            }
            $dataInsertOrder = [
                'client_id'       => $this->getCurrentUser('client_id'),
                'brand_id'        => isset($request['brand_id']) ? $request['brand_id'] : $this->getCurrentUser('brand_id'),
                'employee_id'     => isset($request['supplier_id']) ? $this->getCurrentUser('id') : $employee,
                'customer_id'     => isset($request['supplier_id']) ? $this->getCurrentUser('id') : $customer_id,
                'order_status_id' => $order_status_id,
                'is_paid'         => $is_paid,
                'supplier_id'     => isset($request['supplier_id']) ? $request['supplier_id'] : 0,
                'name'            => isset($request['supplier_id']) ? $supplier_information['name'] : $customer['first_name'] . ' ' . $customer['last_name'],
                'phone'           => isset($request['supplier_id']) ? $supplier_information['phone'] : $customer['phone'],
                'note'            => isset($request['supplier_id']) ? $supplier_information['note'] : '',
                'amount'          => 0,
                'discount'        => 0,
                'created_by'      => $this->getCurrentUser('id')
            ];
            $order           = $this->orderRep->create($dataInsertOrder);

            // Insert user location
            if (!empty($request['supplier_id'])) {
                $dataInsertUserLocation = [
                    'client_id'   => $this->getCurrentUser('client_id'),
                    'order_id'    => $order->id,
                    'name'        => $supplier_information['name'],
                    'address'     => $supplier_information['address'],
                    'wards_id'    => $supplier_information['wards_id'],
                    'district_id' => $supplier_information['district_id'],
                    'city_id'     => $supplier_information['city_id'],
                    'note'        => $supplier_information['note']
                ];
                $this->userLocationRep->create($dataInsertUserLocation);
            }

            // Insert order detail
            $amount      = 0;
            $service_ids = [];
            $product_ids = [];
            foreach ($items as $item) {
                if (isset($item['service_id'])) {
                    $service_ids[] = $item['service_id'];
                }
                if (isset($item['product_id'])) {
                    $product_ids[] = $item['product_id'];
                }
            }

            $products = $this->productRep->findByMany($product_ids)->keyBy('id');
            $services = $this->serviceRep->findByMany($service_ids)->keyBy('id');

            foreach ($items as $item) {
                $flag       = false;
                $product_id = 0;
                $service_id = 0;
                $total      = 0;
                $name       = '';
                $price      = 0;
                if (isset($item['service_id']) && isset($services[$item['service_id']])) {
                    $flag       = true;
                    $service_id = $item['service_id'];
                    $total      = $services[$service_id]->price * $item['quantity'];
                    $name       = $services[$service_id]->name;
                    $price      = $services[$service_id]->price;
                }
                if (isset($item['product_id']) && isset($products[$item['product_id']])) {
                    $flag       = true;
                    $product_id = $item['product_id'];
                    $total      = $products[$product_id]->price * $item['quantity'];
                    $name       = $products[$product_id]->name;
                    $price      = $products[$product_id]->price;
                }

                if ($flag) {
                    $dataInsertOrderDetail = [
                        'order_id'   => $order['id'],
                        'service_id' => $service_id,
                        'product_id' => $product_id,
                        'name'       => $name,
                        'price'      => $price,
                        'quantity'   => $item['quantity'],
                        'total'      => $total
                    ];
                    $this->orderDetailRep->create($dataInsertOrderDetail);
                    $amount += $total;
                }
            }

            // Insert order payment
            if ($is_paid) {
                $dataInsertOrderPayment = [
                    'order_id'          => $order['id'],
                    'payment_method_id' => PAYMENT_METHOD_CASH,
                    'value'             => $amount,
                    'change'            => 0,
                    'total_payment'     => $amount
                ];
                $this->orderPaymentRep->create($dataInsertOrderPayment);
            }

            // Update amount for order
            $this->orderRep->update([
                'amount'   => $amount - (($amount * $discount) / 100),
                'discount' => ($amount * $discount) / 100,
                'code'     => CommonHelper::createRandomCode($order['id'])
            ],
                $order['id']
            );

            $msg        = new stdClass();
            $msg->title = 'Cập nhật đơn hàng Thành công';
            if (!empty($request['supplier_id'])) {
                $msg->content = 'Kiểm tra tình trạng đơn đặt hàng tại Tài khoản > Quản lý đơn đặt hàng';
            } else {
                $msg->content = 'Kiểm tra tình trạng đơn đặt hàng tại trang chủ > Thu ngân';
            }
            $this->setMessage($msg);
            $this->setData($dataInsertOrder);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Cập nhật đơn hàng thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponseData();
    }

    public function updateSupplier(Request $request)
    {
        try {
            $items                = $request['cart_items'];
            $customer             = $request['cart_customer'];
            $discount             = $request['cart_discount'];
            $employee             = $request['cart_employee'];
            $supplier_information = $request['cart_supplier_information'];
            $id                   = $request['id'];

            // Delete all old information with current order
            $this->orderRep->delete(['id' => $id]);
            $this->orderDetailRep->delete(['order_id' => $id]);
            $this->orderDetailRep->delete(['order_id' => $id]);
            if (!empty($request['supplier_id'])) {
                $this->userLocationRep->delete(['order_id' => $id]);
            }

            // Check exist customer of not
            if (!empty($customer)) {
                if (isset($customer['id'])) {
                    $customer_id = $customer['id'];
                } else {
                    // Check exist customer before by phone
                    $customer_query = $this->customerRep->findByAttributes(['phone' => $customer['phone']]);
                    if (count($customer_query) > 0) {
                        $customer_id = $customer_query[0]->id;
                    } else {
                        $dataInsertCustomer = [
                            'client_id'        => $this->getCurrentUser('client_id'),
                            'first_name'       => $customer['first_name'],
                            'last_name'        => $customer['last_name'],
                            'hometown_city_id' => isset($customer['hometown_city_id']) ? $customer['hometown_city_id'] : 0,
                            'city_id'          => isset($customer['city_id']) ? $customer['city_id'] : 0,
                            'district_id'      => isset($customer['district_id']) ? $customer['district_id'] : 0,
                            'wards_id'         => isset($customer['wards_id']) ? $customer['wards_id'] : 0,
                            'phone'            => isset($customer['phone']) ? $customer['phone'] : '',
                            'email'            => isset($customer['email']) ? $customer['email'] : '',
                            'type_id'          => CUSTOMER_NORMAL
                        ];
                        $customer           = $this->customerRep->create($dataInsertCustomer);
                        $customer_id        = $customer['id'];
                    }
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
            // Set status for order
            $order_status_id = ORDER_STATUS_NOT_PAID;
            $is_paid = 0;
            if (isset($request['is_paid'])) {
                $order_status_id = ORDER_STATUS_FINISHED;
                $is_paid = 1;
            }
            $dataInsertOrder = [
                'client_id'       => $this->getCurrentUser('client_id'),
                'brand_id'        => isset($request['brand_id']) ? $request['brand_id'] : $this->getCurrentUser('brand_id'),
                'employee_id'     => isset($request['supplier_id']) ? $this->getCurrentUser('id') : $employee,
                'customer_id'     => isset($request['supplier_id']) ? $this->getCurrentUser('id') : $customer_id,
                'order_status_id' => $order_status_id,
                'is_paid'         => $is_paid,
                'supplier_id'     => isset($request['supplier_id']) ? $request['supplier_id'] : 0,
                'name'            => isset($request['supplier_id']) ? $supplier_information['name'] : $customer['first_name'] . ' ' . $customer['last_name'],
                'phone'           => isset($request['supplier_id']) ? $supplier_information['phone'] : $customer['phone'],
                'note'            => isset($request['supplier_id']) ? $supplier_information['note'] : '',
                'amount'          => 0,
                'discount'        => 0,
                'created_by'      => $this->getCurrentUser('id')
            ];
            $order           = $this->orderRep->create($dataInsertOrder);

            // Insert user location
            if (!empty($request['supplier_id'])) {
                $dataInsertUserLocation = [
                    'client_id'   => $this->getCurrentUser('client_id'),
                    'order_id'    => $order->id,
                    'name'        => $supplier_information['name'],
                    'address'     => $supplier_information['address'],
                    'wards_id'    => $supplier_information['wards_id'],
                    'district_id' => $supplier_information['district_id'],
                    'city_id'     => $supplier_information['city_id'],
                    'note'        => $supplier_information['note']
                ];
                $this->userLocationRep->create($dataInsertUserLocation);
            }

            // Insert order detail
            $amount      = 0;
            $service_ids = [];
            $product_ids = [];
            foreach ($items as $item) {
                if (isset($item['service_id'])) {
                    $service_ids[] = $item['service_id'];
                }
                if (isset($item['product_id'])) {
                    $product_ids[] = $item['product_id'];
                }
            }

            $products = $this->productRep->findByMany($product_ids)->keyBy('id');
            $services = $this->serviceRep->findByMany($service_ids)->keyBy('id');

            foreach ($items as $item) {
                $flag       = false;
                $product_id = 0;
                $service_id = 0;
                $total      = 0;
                $name       = '';
                $price      = 0;
                if (isset($item['service_id']) && isset($services[$item['service_id']])) {
                    $flag       = true;
                    $service_id = $item['service_id'];
                    $total      = $services[$service_id]->price * $item['quantity'];
                    $name       = $services[$service_id]->name;
                    $price      = $services[$service_id]->price;
                }
                if (isset($item['product_id']) && isset($products[$item['product_id']])) {
                    $flag       = true;
                    $product_id = $item['product_id'];
                    $total      = $products[$product_id]->price * $item['quantity'];
                    $name       = $products[$product_id]->name;
                    $price      = $products[$product_id]->price;
                }

                if ($flag) {
                    $dataInsertOrderDetail = [
                        'order_id'   => $order['id'],
                        'service_id' => $service_id,
                        'product_id' => $product_id,
                        'name'       => $name,
                        'price'      => $price,
                        'quantity'   => $item['quantity'],
                        'total'      => $total
                    ];
                    $this->orderDetailRep->create($dataInsertOrderDetail);
                    $amount += $total;
                }
            }

            // Insert order payment
            if ($is_paid) {
                $dataInsertOrderPayment = [
                    'order_id'          => $order['id'],
                    'payment_method_id' => PAYMENT_METHOD_CASH,
                    'value'             => $amount,
                    'change'            => 0,
                    'total_payment'     => $amount
                ];
                $this->orderPaymentRep->create($dataInsertOrderPayment);
            }

            // Update amount for order
            $this->orderRep->update([
                'amount'   => $amount - (($amount * $discount) / 100),
                'discount' => ($amount * $discount) / 100,
                'code'     => CommonHelper::createRandomCode($order['id'])
            ],
                $order['id']
            );

            $msg        = new stdClass();
            $msg->title = 'Cập nhật đơn hàng Thành công';
            if (!empty($request['supplier_id'])) {
                $msg->content = 'Kiểm tra tình trạng đơn đặt hàng tại Tài khoản > Quản lý đơn đặt hàng';
            } else {
                $msg->content = 'Kiểm tra tình trạng đơn đặt hàng tại trang chủ > Thu ngân';
            }
            $this->setMessage($msg);
            $this->setData($dataInsertOrder);
        } catch (Exception $ex) {
            $errors          = new stdClass();
            $errors->title   = 'Cập nhật đơn hàng thất bại';
            $errors->message = $ex->getMessage();
            $this->setMessageDataStatusCode(null, ['errors' => $errors], Response::HTTP_INTERNAL_SERVER_ERROR);
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

    public function getDetail($id)
    {

        $data = $this->orderRep->getDetail($id);

        $order_details = $this->orderDetailRep->getByOrderId($id);

        if (empty($data->supplier_id)) {
            unset($data->information_receive_name);
            unset($data->information_receive_phone);
            unset($data->information_receive_address);
            unset($data->information_receive_city_name);
            unset($data->information_receive_district_name);
            unset($data->information_receive_wards_name);
            unset($data->information_receive_note);
            unset($data->supplier_name);
        }

        // Convert into customer object
        $obj = new stdClass();
        $obj->name = $data->name;
        unset($data->name);
        $obj->phone = $data->phone;
        unset($data->phone);
        $obj->email = $data->email;
        unset($data->email);
        $obj->id = $data->customer_id;
        unset($data->customer_id);
        $data->customer = $obj;

        // Convert into employee object
        $obj_employee = new stdClass();
        $obj_employee->first_name = $data->employee_first_name;
        unset($data->employee_first_name);
        $obj_employee->last_name = $data->employee_last_name;
        unset($data->employee_last_name);
        $obj_employee->id = $data->employee_id;
        unset($data->employee_id);
        $data->employee = $obj_employee;

        // Convert into brand object
        $obj_brand = new stdClass();
        $obj_brand->name = $data->brand_name;
        unset($data->brand_name);
        $obj_brand->id = $data->brand_id;
        unset($data->brand_id);
        $data->brand = $obj_brand;

        if (!empty($data)) {
            $data->order_details = $order_details;
        }

        $this->setData($data);

        return $this->getResponseData();
    }

    public function updateStatus($params)
    {
        $this->orderRep->update(["order_status_id" => $params['order_status_id']], $params['id']);
        $msg          = new stdClass();
        $msg->title   = 'Thành công';
        $msg->content = 'Cập nhật trạng thái đơn hàng thành công';
        $this->setMessage($msg);
        $this->setStatusCode(Response::HTTP_OK);

        return $this->getResponseData();
    }

}
