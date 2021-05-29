<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;

class ReportService extends BaseService
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

    public function getTotal(Request $request)
    {
        $data = $this->orderRep->getTotal($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getTotalByEmployeeAndDate(Request $request)
    {
        $data = $this->orderRep->getTotalByEmployeeAndDate($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getTotalByServiceAndDate(Request $request)
    {
        $data = $this->orderRep->getTotalByServiceAndDate($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getTotalByProductAndDate(Request $request)
    {
        $data = $this->orderRep->getTotalByProductAndDate($request->all());
        $this->setData($data);

        return $this->getResponseData();
    }

}
