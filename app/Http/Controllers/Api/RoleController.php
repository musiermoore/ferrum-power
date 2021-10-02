<?php

namespace App\Http\Controllers\Api;

use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    public function getListRoles()
    {
        $roles = Role::all();

        return response()->json([
            'roles' => $roles
        ]);
    }
}
