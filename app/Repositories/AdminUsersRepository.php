<?php

namespace App\Repositories;

use App\Models\AdminUsers;
use Illuminate\Support\Facades\DB;

class AdminUsersRepository extends BaseRepository
{
    public function __construct(AdminUsers $model)
    {
        parent::__construct($model);
    }

}
