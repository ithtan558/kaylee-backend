<?php

namespace App\Services;

use App\Repositories\LogRepository;
use Illuminate\Http\Request;

class LogService extends BaseService
{
    protected $logRep;

    public function __construct(LogRepository $logRep)
    {
        $this->logRep = $logRep;
    }

}
