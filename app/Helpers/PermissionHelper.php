<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PermissionHelper
{
    public static function getPermissions($moduleName)
    {
        $userRole = Session::get('role');
        $userId   = Session::get('user_id');

        /* ================= ADMIN ================= */
        if ($userRole === 'admin') {
            return (object)[
                'can_view'     => true,
                'can_create'   => true,
                'can_edit'     => true,
                'can_delete'   => true,
                'can_approve'  => true,
                'can_download'=> true,
                'can_export'   => true,
            ];
        }

        /* ================= EMPLOYEE ================= */
        if ($userRole === 'employee' && $userId) {

            $permission = DB::table('employee_module_access')
                ->where('employee_id', $userId)
                ->where('module_name', $moduleName)
                ->first();

            // If module not assigned → no access
            if (!$permission) {
                return self::emptyPermissions();
            }

            return (object)[
                'can_view'      => (bool) ($permission->can_view ?? 0),
                'can_create'    => (bool) ($permission->can_create ?? 0),
                'can_edit'      => (bool) ($permission->can_edit ?? 0),
                'can_delete'    => (bool) ($permission->can_delete ?? 0),
                'can_approve'   => (bool) ($permission->can_approve ?? 0),
                'can_download'  => (bool) ($permission->can_download ?? 0),
                'can_export'    => (bool) ($permission->can_export ?? 0),
            ];
        }

        /* ================= DEFAULT ================= */
        return self::emptyPermissions();
    }

    /* ================= DEFAULT EMPTY ================= */
    private static function emptyPermissions()
    {
        return (object)[
            'can_view'     => false,
            'can_create'   => false,
            'can_edit'     => false,
            'can_delete'   => false,
            'can_approve'  => false,
            'can_download'=> false,
            'can_export'   => false,
        ];
    }
}
