<?php

namespace App\Repositories;

use App\Models\Content;

class ContentRepository extends BaseRepository
{
    public function __construct(Content $model)
    {
        parent::__construct($model);
    }

    public function getAll()
    {
        $result = $this->model
            ->select('*')
            ->where('is_active', STATUS_ACTIVE)
            ->orderBy('name', 'DESC')
            ->get();

        return $result;
    }

    public function getDetail($slug)
    {
        $query = $this->model
            ->select("id", "name", "code", "description", "content", "image")
            ->where('slug', $slug)
            ->first();

        return $query;
    }

}
