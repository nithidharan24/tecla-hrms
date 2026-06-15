<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Timesheet extends Model
{
    use HasFactory;

    protected $table = 'timesheets';

    protected $fillable = [
        'employee_id',
        'project_id',
        'date',
        'hours',
        'description',
        'comments',
        'status',
        'week_start',
        'week_end',
        'approved_by',
        'approved_at',
        'rejection_reason'
    ];

    protected $casts = [
        'date' => 'date',
        'week_start' => 'date',
        'week_end' => 'date',
        'approved_at' => 'datetime',
        'hours' => 'decimal:2'
    ];

    /**
     * Get the employee that owns the timesheet
     */
    public function employee()
    {
        return $this->belongsTo(AllEmployee::class, 'employee_id');
    }

    /**
     * Get the project that owns the timesheet
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the approver (admin) who approved/rejected the timesheet
     */
    public function approver()
    {
        return $this->belongsTo(AllEmployee::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForWeek($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Get employee name directly from database
     */
    public function getEmployeeNameAttribute()
    {
        if (!$this->employee_id) {
            return 'N/A';
        }
        
        $employee = DB::table('allemployees')
            ->where('id', $this->employee_id)
            ->first();
            
        return $employee ? ($employee->firstname . ' ' . $employee->lastname) : 'N/A';
    }

    /**
     * Get employee email directly from database
     */
    public function getEmployeeEmailAttribute()
    {
        if (!$this->employee_id) {
            return null;
        }
        
        $employee = DB::table('allemployees')
            ->where('id', $this->employee_id)
            ->first();
            
        return $employee ? $employee->email : null;
    }

    /**
     * Get approver name directly from database
     */
    public function getApproverNameAttribute()
    {
        if (!$this->approved_by) {
            return null;
        }
        
        $approver = DB::table('allemployees')
            ->where('id', $this->approved_by)
            ->first();
            
        return $approver ? ($approver->firstname . ' ' . $approver->lastname) : null;
    }
}