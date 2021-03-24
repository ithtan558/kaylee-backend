<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Repositories\AdminUsersRepository;
use Illuminate\Http\Request;

class AdminUsersService extends BaseService
{
    protected $adminUsersRep;

    public function __construct(AdminUsersRepository $adminUsersRep)
    {
        $this->adminUsersRep = AdminUsersService;
    }

}
