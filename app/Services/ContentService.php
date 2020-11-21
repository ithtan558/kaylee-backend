<?php

namespace App\Services;

use App\Libraries\Api;
use App\Repositories\ContentRepository;
use Illuminate\Http\Request;

class ContentService extends BaseService
{
    protected $contentRep;

    public function __construct(ContentRepository $contentRep)
    {
        $this->contentRep = $contentRep;
    }

    public function getAll()
    {
        $data = $this->contentRep->getAll();
        $this->setData($data);

        return $this->getResponseData();
    }

    public function getDetail($slug)
    {
        $data = $this->contentRep->getDetail($slug);

        $this->setData($data);

        return $this->getResponseData();
    }

}
