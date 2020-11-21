<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContentService;
use App\Libraries\Api;

class ContentController extends Controller
{

    protected $request;
    protected $contentService;

    public function __construct(Request $request, ContentService $contentService)
    {
        $this->request        = $request;
        $this->contentService = $contentService;
    }

    public function getAll()
    {
        $data = $this->contentService->getAll();

        return Api::response($data[RESPONSE_KEY], $data[STT_CODE_KEY]);
    }

    public function getDetail($slug)
    {
        $result = $this->contentService->getDetail($slug);
        return Api::response($result[RESPONSE_KEY], $result[STT_CODE_KEY]);
    }

}
