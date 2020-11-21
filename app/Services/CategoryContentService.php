<?php

namespace App\Services;

use App\Repositories\CategoryContentRepository;
use Illuminate\Http\Request;

class CategoryContentService extends BaseService
{
    protected $categoryContentRep;

    public function __construct(CategoryContentRepository $categoryContentRep)
    {
        $this->categoryContentRep = $categoryContentRep;
    }

    public function getAll()
    {
        $data = $this->categoryContentRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

}
