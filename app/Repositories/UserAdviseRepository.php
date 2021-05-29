<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\UserAdvise;

class UserAdviseRepository extends BaseRepository
{
    public function __construct(UserAdvise $model)
    {
        parent::__construct($model);
    }

    protected function getFieldSearchAble()
    {
        return [
            'keyword' => [
                'field'   => [UserAdvise::getCol('name')],
                'compare' => 'like',
                'type'    => 'string',
            ],
        ];
    }

}
