<?php

namespace App\Http\Controllers\Api;

use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    public function getListRoles()
    {
        $roles = Role::all();

        $data = [
            'roles' => $roles
        ];

        return $this->successResponse(200, null, $data);
    }
}
