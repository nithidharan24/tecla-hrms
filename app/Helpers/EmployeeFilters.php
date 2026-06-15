<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
if (!function_exists('getAdminBranchFilter')) {
    function getAdminBranchFilter()
    {
        $role = Session::get('role');
        $employeeId = Session::get('user_id');

        if ($role === 'admin') {
            return null;
        }

        // manager: no branch restriction — they filter by department
        if ($role === 'manager') {
            return null;
        }

        if ($role === 'employee' && $employeeId) {
            return DB::table('allemployees')
                ->where('id', $employeeId)
                ->value('branch_id') ?: null;
        }

        return null;
    }
}


if (!function_exists('getEmployeeDepartmentFilter')) {
    function getEmployeeDepartmentFilter()
    {
        $role = Session::get('role');

        if ($role === 'admin') {
            return null;
        }

        $employeeId = Session::get('user_id');
        if ($employeeId) {
            $employee = DB::table('allemployees')->find($employeeId);
            // manager role: return their department so attendance/leaves/employees
            // are scoped to their department only
            if ($role === 'manager') {
                return $employee ? $employee->department : null;
            }
            return $employee ? $employee->department : null;
        }

        return null;
    }
}
if (!function_exists('getManagerTeamFilter')) {
    function getManagerTeamFilter()
    {
        $role = Session::get('role');

        if ($role === 'admin') {
            return null;
        }

        // manager role uses department filter, not manager_id filter
        if ($role === 'manager') {
            return null;
        }

        $employeeId = Session::get('user_id');

        if (!$employeeId) {
            return null;
        }

        $employee = DB::table('allemployees')
            ->leftJoin('designation', 'allemployees.designation', '=', 'designation.id')
            ->where('allemployees.id', $employeeId)
            ->select('allemployees.id', 'designation.designation as designation_name')
            ->first();

        if (!$employee) {
            return null;
        }

        if (stripos($employee->designation_name, 'manager') !== false) {
            return $employee->id;
        }

        return null;
    }
}
if (!function_exists('applyBranchFilter')) {
    /**
     * Apply branch restriction to a given query based on session data.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param string $tableAlias Optional table alias if branch_id column is prefixed
     * @return mixed
     */
    function applyBranchFilter($query, $tableAlias = null)
    {
        $role = Session::get('role');
        $branchId = Session::get('branch_id');

        // Admin role: no branch restriction
        if ($role === 'admin') {
            return $query;
        }

        // Employee: restrict by branch_id from session
        if ($role === 'employee' && $branchId) {
            $column = $tableAlias ? "{$tableAlias}.branch_id" : "branch_id";
            return $query->where($column, $branchId);
        }

        // If no role or no branch in session, return query unchanged
        return $query;
    }
}

