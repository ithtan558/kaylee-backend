<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Brand;
use App\Models\City;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Role;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserLocation;
use App\Models\UserRole;
use App\Models\Wards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getList($params)
    {

        $user  = CommonHelper::getAuth();
        $order  = 'id';
        $length = $this->getLength($params);
        $sort   = $this->getOrder($params);

        $query = $this->model->select(
            Order::getCol('id'),
            Order::getCol('code'),
            Order::getCol('name'),
            Order::getCol('amount'),
            Order::getCol('order_status_id'),
            Order::getCol('created_at'),
            Supplier::getCol('name as supplier_name')
        )
        ->leftJoin(Supplier::getTbl(), Supplier::getCol("id"), "=", Order::getCol("supplier_id"))
        ->with("order_details");
        $query = $this->addConditionToQuery($query, $params, []);

        if (isset($params['is_history_by_supplier'])) {
            $query = $query->where('customer_id', $user->id);
            $query = $query->where('supplier_id', '>',  0);
        }
        if (isset($params['is_history'])) {
            $query = $query->whereIn('order_status_id', [ORDER_STATUS_FINISHED, ORDER_STATUS_CANCEL]);
        }

        // Filter base on roles of user
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }

        if (in_array(ROLE_BRAND_MANAGER, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        }

        if (!empty($params['order_status_id'])) {
            $query = $query->where('order_status_id', $params['order_status_id']);
        }

        if (isset($params['start_date'])) {
            $query->whereRaw('date('.Order::getCol('created_at').') between "' . $params['start_date'] . '" and "' . $params['end_date'] . '"');
        }
        $query = $query->orderBy($order, $sort)
            ->paginate($length);

        return $this->formatPagination($query);

    }

    public function getListOrderDetailByOrderIds($order_ids)
    {

        $query = $this->model->select(
            Order::getCol('id'),
            Order::getCol('code'),
            Order::getCol('name'),
            Order::getCol('order_status_id'),
            Supplier::getCol('name as supplier_name')
        );
        $query = $query->get();

        return $query;

    }

    public function getAll()
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE);

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        }

        $result = $query->orderBy('id', 'DESC')->get();

        return $result;
    }

    public function getCount()
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*');

        if (in_array(ROLE_MANAGER, $roles) || in_array(ROLE_BRAND_MANAGER, $roles) || in_array(ROLE_EMPLOYEE, $roles)) {
            $query = $query->where('brand_id', $user->brand_id);
        }

        $result = $query->count();

        return $result;
    }

    public function getTotalByEmployeeAndDate($params)
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select(
                DB::raw('SUM(' . Order::getCol('amount') . ') as amount'),
                User::getCol('first_name'),
                User::getCol('last_name'),
                User::getCol('phone')
            )
            ->from(Order::getTbl())
            ->join(User::getTbl(), User::getCol("id"), "=", Order::getCol("employee_id"));

        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where(Order::getCol('client_id'), $user->client_id);
        }
        if (isset($params['brand_id']) && $params['brand_id']) {
            $query = $query->where(Order::getCol('brand_id'), $params['brand_id']);
        }

        $query->whereRaw('date(' . Order::getCol('created_at') . ') between "' . $params['start_date'] . '" and "' . $params['end_date'] . '"');

        $result = $query->orderBy(Order::getCol('id'), 'DESC')
            ->groupBy(Order::getCol('employee_id'))->get();

        return $result;

    }

    public function getTotalByServiceAndDate($params)
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select(
                DB::raw('SUM(' . OrderDetail::getCol('price') . ') as price'),
                DB::raw('SUM(' . OrderDetail::getCol('quantity') . ') as quantity'),
                Service::getCol('name')
            )
            ->from(OrderDetail::getTbl())
            ->join(Order::getTbl(), Order::getCol("id"), "=", OrderDetail::getCol("order_id"))
            ->join(Service::getTbl(), Service::getCol("id"), "=", OrderDetail::getCol("service_id"));

        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where(Order::getCol('client_id'), $user->client_id);
        }
        if (isset($params['brand_id']) && $params['brand_id']) {
            $query = $query->where(Order::getCol('brand_id'), $params['brand_id']);
        }

        $query->whereRaw('date(' . Order::getCol('created_at') . ') between "' . $params['start_date'] . '" and "' . $params['end_date'] . '"');

        $result = $query->orderBy(Order::getCol('id'), 'DESC')
            ->groupBy(OrderDetail::getCol('service_id'))->get();

        foreach ($result as &$item) {
            $item->amount = $item->price * $item->quantity;
        }

        return $result;

    }

    public function getDetail($id)
    {
        $query = $this->model
            ->select(
                Order::getCol('id'),
                Order::getCol('code'),
                Order::getCol('is_paid'),
                Order::getCol('name'),
                Order::getCol('phone'),
                Order::getCol('email'),
                Order::getCol('note'),
                Order::getCol('amount'),
                Order::getCol('customer_id'),
                Order::getCol('sub_total'),
                Order::getCol('discount'),
                Order::getCol('tax_value'),
                Order::getCol('order_status_id'),
                Order::getCol('supplier_id'),
                User::getCol('first_name as employee_first_name'),
                User::getCol('last_name as employee_last_name'),
                Order::getCol('employee_id'),
                Brand::getCol('name as brand_name'),
                Order::getCol('brand_id'),
                Supplier::getCol('name as supplier_name'),
                UserLocation::getCol('name as information_receive_name'),
                Order::getCol('phone as information_receive_phone'),
                UserLocation::getCol('name as information_receive_address'),
                City::getCol('name as information_receive_city_name'),
                District::getCol('name as information_receive_district_name'),
                Wards::getCol('name as information_receive_wards_name'),
                UserLocation::getCol('note as information_receive_note')
            )
            ->join(Brand::getTbl(), Brand::getCol('id'), '=', Order::getCol('brand_id'))
            ->leftJoin(User::getTbl(), User::getCol('id'), '=', Order::getCol('employee_id'))
            ->leftJoin(Supplier::getTbl(), Supplier::getCol('id'), '=', Order::getCol('supplier_id'))
            ->leftJoin(UserLocation::getTbl(), UserLocation::getCol('order_id'), '=', Order::getCol('id'))
            ->leftJoin(City::getTbl(), City::getCol('id'), '=', UserLocation::getCol('city_id'))
            ->leftJoin(District::getTbl(), District::getCol('id'), '=', UserLocation::getCol('district_id'))
            ->leftJoin(Wards::getTbl(), Wards::getCol('id'), '=', UserLocation::getCol('wards_id'))
            ->where(Order::getCol('id'), $id)
            ->first();

        return $query;
    }

    public function getTotal($params)
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*');

        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }
        if (isset($params['brand_id']) && $params['brand_id']) {
            $query = $query->where('brand_id', $params['brand_id']);
        }

        if (isset($params['start_date'])) {
            $query->whereRaw('date(created_at) between "' . $params['start_date'] . '" and "' . $params['end_date'] . '"');
        }

        $result = $query->orderBy('id', 'DESC')->get();

        $total_value = 0;
        foreach ($result as $item) {
            $total_value+= $item->amount;
        }


        return ['total_value' => $total_value];
    }

    public function getTotalCommission($params)
    {
        // Filter base on roles of user
        $user  = CommonHelper::getAuth();
        $roles = [];
        foreach ($user->user_roles as $role) {
            $roles[] = $role->role_id;
        }

        $query = $this->model
            ->select('*');

        if (in_array(ROLE_MANAGER, $roles)) {
            $query = $query->where('client_id', $user->client_id);
        }
        if (isset($params['brand_id']) && $params['brand_id']) {
            $query = $query->where('brand_id', $params['brand_id']);
        }

        $query = $query->where('employee_id', $params['user_id']);

        if (isset($params['start_date'])) {
            $query->whereRaw('date(created_at) between "' . $params['start_date'] . '" and "' . $params['end_date'] . '"');
        }

        $query = $query->with('order_details');
        $result = $query->orderBy('id', 'DESC')->get();


        return $result;
    }

}
