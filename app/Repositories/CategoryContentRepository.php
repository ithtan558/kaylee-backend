<?php

namespace App\Repositories;

use App\Models\CategoryContent;

class CategoryContentRepository extends BaseRepository
{
    public function __construct(CategoryContent $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        $result = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE)
            ->where('is_delete', STATUS_INACTIVE)
            ->orderBy('name', 'DESC')
            ->get();

        return $result;
    }

}
