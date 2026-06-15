<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class TicketsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $role;
    protected $employeeId;
    protected $filters;

    public function __construct($role, $employeeId, $filters = [])
    {
        $this->role = $role;
        $this->employeeId = $employeeId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('tickets')
            ->leftJoin('allemployees as assign1', 'tickets.assign_1', '=', 'assign1.id')
            ->leftJoin('allemployees as assign2', 'tickets.assign_2', '=', 'assign2.id')
            ->leftJoin('allemployees as assign3', 'tickets.assign_3', '=', 'assign3.id')
            ->leftJoin('allemployees as creator', 'tickets.created_by', '=', 'creator.id')
            ->select(
                'tickets.*',
                'assign1.firstname as assign1_firstname',
                'assign1.lastname as assign1_lastname',
                'assign2.firstname as assign2_firstname',
                'assign2.lastname as assign2_lastname',
                'assign3.firstname as assign3_firstname',
                'assign3.lastname as assign3_lastname',
                'creator.firstname as creator_firstname',
                'creator.lastname as creator_lastname'
            );

        if ($this->role === 'employee') {
            $query->where(function($q) {
                $q->where('tickets.assign_1', $this->employeeId)
                  ->orWhere('tickets.assign_2', $this->employeeId)
                  ->orWhere('tickets.assign_3', $this->employeeId)
                  ->orWhere('tickets.created_by', $this->employeeId);
            });
        }

        // Apply filters
        if (!empty($this->filters['status'])) {
            $query->where('tickets.status', $this->filters['status']);
        }

        if (!empty($this->filters['priority'])) {
            $query->where('tickets.priority', $this->filters['priority']);
        }

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('tickets.created_at', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('tickets.created_at', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['assignee_id'])) {
            $assigneeId = $this->filters['assignee_id'];
            $query->where(function($q) use ($assigneeId) {
                $q->where('tickets.assign_1', $assigneeId)
                  ->orWhere('tickets.assign_2', $assigneeId)
                  ->orWhere('tickets.assign_3', $assigneeId);
            });
        }

        if (!empty($this->filters['creator_id'])) {
            $query->where('tickets.created_by', $this->filters['creator_id']);
        }

        return $query->orderBy('tickets.created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'Subject',
            'Description',
            'Status',
            'Priority',
            'Created At',
            'Updated At',
            'Created By',
            'Assignee 1',
            'Assignee 2',
            'Assignee 3',
            'Resolution Time (Hours)'
        ];
    }

    public function map($ticket): array
    {
        $assignee1 = $ticket->assign1_firstname ? $ticket->assign1_firstname . ' ' . $ticket->assign1_lastname : '-';
        $assignee2 = $ticket->assign2_firstname ? $ticket->assign2_firstname . ' ' . $ticket->assign2_lastname : '-';
        $assignee3 = $ticket->assign3_firstname ? $ticket->assign3_firstname . ' ' . $ticket->assign3_lastname : '-';
        $creator = $ticket->creator_firstname . ' ' . $ticket->creator_lastname;
        
        $resolutionTime = $ticket->status === 'closed' 
            ? round((strtotime($ticket->updated_at) - strtotime($ticket->created_at)) / 3600, 2)
            : null;

        return [
            $ticket->ticket_id,
            $ticket->ticket_subject,
            $ticket->description,
            ucfirst($ticket->status),
            $ticket->priority,
            $ticket->created_at,
            $ticket->updated_at,
            $creator,
            $assignee1,
            $assignee2,
            $assignee3,
            $resolutionTime
        ];
    }
}