<?php

namespace App\Http\Controllers\Backend\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PDF;

class AttendanceReportController extends Controller
{
    /**
     * Display a listing of attendance reports.
     */
    public function index(Request $request)
    {
        $employeeName = $request->input('employee_name');
        $month = $request->input('month');
        $year = $request->input('year');

        $query = DB::table('attendance_punch')
            ->join('allemployees', 'allemployees.id', '=', 'attendance_punch.user_id')
            ->select(
                'attendance_punch.*',
                DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname) AS employee_name")
            )
            ->when($employeeName, function ($query, $employeeName) {
                return $query->where(DB::raw("CONCAT(allemployees.firstname, ' ', allemployees.lastname)"), 'like', "%$employeeName%");
            })
            ->when($month, function ($query, $month) {
                return $query->whereRaw('MONTH(attendance_punch.date) = ?', [$month]);
            })
            ->when($year, function ($query, $year) {
                return $query->whereRaw('YEAR(attendance_punch.date) = ?', [$year]);
            })
            ->orderBy('attendance_punch.date', 'asc');

        // Handle exports
        if ($request->has('export') || $request->has('pdf')) {
            $attendanceData = $query->get();
            
            if ($request->export == 'csv') {
                return $this->exportCSV($attendanceData);
            }
            
            if ($request->pdf) {
                return $this->exportPDF($attendanceData, $request);
            }
        }

        $attendanceData = $query->paginate(25);

        return view('hrms.hr.Reports.attendance-reports.index', compact('attendanceData'));
    }

    protected function exportCSV($attendanceData)
    {
        $headers = [
            'Employee Name', 'Date', 'Clock In', 'Clock Out', 'Work Status'
        ];

        $data = [];
        foreach ($attendanceData as $record) {
            $workStatus = (is_null($record->punch_in) && is_null($record->punch_out)) ? 'Week Off' : 'Present';
            
            $data[] = [
                $record->employee_name,
                \Carbon\Carbon::parse($record->date)->format('d M Y'),
                $record->punch_in ?? '-',
                $record->punch_out ?? '-',
                $workStatus
            ];
        }

        return response()->streamDownload(function() use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'attendance_report_'.date('Y-m-d').'.csv');
    }

    protected function exportPDF($attendanceData, $request)
    {
        $pdf = PDF::loadView('hrms.hr.Reports.attendance-reports.pdf', [
            'attendanceData' => $attendanceData,
            'filters' => $request->all()
        ]);
        
        return $pdf->download('attendance_report_'.date('Y-m-d').'.pdf');
    }
}