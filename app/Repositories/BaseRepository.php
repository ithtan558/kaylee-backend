<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\CommonHelper;

/**
 * Class BaseRepository
 * @package App\Repositories
 */
class BaseRepository
{
    /**
     * @var Model Model
     */
    protected $model;

    /**
     * @var array $limitPerPage The limitation of record on per page
     */
    protected $limitPerPage = [10, 20, 30, 40, 50, 100, 200];

    /**
     * @var array $softAble Allowing order asc or desc
     */
    protected $orderAble = ['DESC', 'desc', 'ASC', 'asc'];

    /**
     * EloquentRepository constructor.
     *
     * @param $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function all()
    {
        return $this->model->orderBy($this->model->getKeyName(), 'DESC')->get();
    }

    public function insert($data)
    {
        return $this->model->insert($data);
    }

    public function insertMultiple(array $data)
    {
        return DB::table($this->model->getTable())->insert($data);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $dataUpdate = array_intersect_key($data, array_flip($this->model->getFillable()));

        return $this->model->where($this->model->getKeyName(), $id)->update($dataUpdate);
    }

    public function updateByMultipleCondition($data, array $conditions = [])
    {
        if (count($conditions)) {
            return $this->model->where($conditions)->update($data);
        } else {
            return $this->model->update($data);
        }
    }

    public function save($model, $data)
    {
        $model->update($data);

        return $model;
    }

    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

    public function delete($where)
    {
        $this->model->where($where);
        return $this->model->delete();
    }

    public function findByAttributes(array $attributes)
    {
        return $this->model->where($attributes)->get();
    }

    public function findByAUserId($user_id)
    {
        return $this->model->where('user_id', $user_id)->first();
    }

    public function findByCode($code)
    {
        return $this->model->where('code', $code)->first();
    }


    public function findByMany(array $ids)
    {
        $query = $this->model->query();

        return $query->whereIn($this->model->getKeyName(), $ids)->get();
    }

    public function pagination()
    {
        return $this->model->filterPaginateOrder();
    }

    public function exists(array $condition)
    {
        $result = $this->model->where($condition)->first();

        return count($result) > 0;
    }

    public function getConstant($key)
    {
        return constant('self::' . $key);
    }

    public function getKeyValue($key, $value)
    {
        $items = $this->all();
        $data  = array();
        foreach ($items as $item) {
            $data[$item->$key] = $item->$value;
        }
        return $data;
    }

    public function formatPagination(LengthAwarePaginator $data)
    {
        $length      = $data->perPage();
        $totalRecord = $data->total();
        $result      = [
            'page'  => $data->currentPage(),
            'limit' => (int)$length,
            'total' => $totalRecord,
            'pages' => ceil($totalRecord / $length),
            'items' => $data->items(),
        ];

        return $result;
    }

    /**
     * @param $query
     * @param $search
     * @param $fieldSearch
     *
     * @return mixed
     */
    protected function addConditionToQuery($query, $search, $fieldSearch)
    {
        if (!$search) {
            return $query;
        }
        foreach ($search as $key => $val) {
            if (!isset($fieldSearch[$key]) || empty($val)) {
                continue;
            }

            $field = $fieldSearch[$key]['field'];
            switch ($fieldSearch[$key]['type']) {
                case 'string':
                    $compare = empty($fieldSearch[$key]['compare']) ? 'like' : $fieldSearch[$key]['compare'];

                    if (strtolower(trim($compare)) == 'like') {
                        $val = '%' . $val . '%';
                    }
                    if (is_array($fieldSearch[$key]['field'])) {
                        $where_raw = '';
                        foreach ($fieldSearch[$key]['field'] as $index => $item) {
                            if ($index == 0) {
                                $where_raw .= "( ".DB::raw($item) . " " . $compare . " '" . $val . "'";
                            } else {
                                $where_raw .= "or ".DB::raw($item) . " " . $compare . " '" . $val . "'";
                            }
                        }
                        $where_raw .= ")";
                        $query->whereRaw($where_raw);
                    }

                    break;

                case 'date':
                    $val        = str_replace('"', '', $val);
                    $dateFormat = date('Y-m-d', strtotime($val));
                    $query->where(
                        DB::raw('DATE(' . $field . ')'), $fieldSearch[$key]['compare'], $dateFormat);
                    break;

                case 'array':
                    $delimiter = empty($fieldSearch[$key]['delimiter']) ? "," : $fieldSearch[$key]['delimiter'];
                    $val       = explode($delimiter, $val);
                    $query->whereIn($field, $val);
                    break;

                default:
                    $query->where($fieldSearch[$key]['field'], $fieldSearch[$key]['compare'], $val);
                    break;
            }
        }
        return $query;
    }

    protected function filterDataByAuthUser($query, $aliasColumn)
    {
        $authUser = CommonHelper::getAuth();

        if ($authUser && !in_array($authUser->roles_id, [ROLES_ROOT, ROLES_ADMIN])) {
            $query->where($aliasColumn, STATUS_INACTIVE);
        }

        return $query;
    }

    public function generateCode($prefixCode, $codeStart = '00001')
    {
        $num = $codeStart;
        $obj = $this->model->orderBy('id', 'DESC')->first();

        if ($obj) {
            $objCode = $obj->code;
            $objCode = explode('-', $objCode);
            if (count($objCode) >= 2) {
                $num = $objCode[1] + 1;
                $num = str_pad($num, 5, '0', STR_PAD_LEFT);
            }
        }

        $codeNext = $prefixCode . $num;

        return $codeNext;
    }

    public function getLength($params)
    {
        return isset($params['limit']) && in_array($params['limit'], $this->limitPerPage) ? $params['limit'] : $this->limitPerPage[0];
    }

    public function getOrder($params)
    {
        return isset($params['order']) && in_array($params['order'], $this->orderAble) ? $params['order'] : $this->orderAble[2];
    }

    public function getSortColumn($params, $defaultSorting = '', $sortableColumn = [])
    {
        $sortableField = $sortableColumn ? $sortableColumn : $this->model->getFillable();
        $defaultField  = $defaultSorting ? $defaultSorting : $this->model->getKeyName();

        return isset($params['sort']) && in_array($params['sort'], $sortableField) ? $params['sort'] : $defaultField;
    }
}
