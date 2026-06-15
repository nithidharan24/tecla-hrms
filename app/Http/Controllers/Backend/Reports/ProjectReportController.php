<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use PDF;

class ProjectReportController extends Controller
{
    public function index(Request $request)
    {
        $projectNames = DB::table('projects')->pluck('projectname')->all();
        $projects = $this->getFilteredProjects($request);
        
        foreach ($projects as $project) {
            $teamIds = explode(',', $project->team);
            $teamMembers = DB::table('allemployees')
                ->whereIn('id', $teamIds)
                ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                ->pluck('name')
                ->toArray();

            // Format as numbered list
            if (!empty($teamMembers)) {
                $formattedNames = '';
                foreach ($teamMembers as $index => $name) {
                    $formattedNames .= ($index + 1) . '. ' . $name . '<br>';
                }
                $project->teamNames = $formattedNames;
            } else {
                $project->teamNames = 'N/A';
            }
        }

        return view('hrms.hr.Reports.projects-reports.index', compact('projects', 'projectNames'));
    }

    public function exportCSV(Request $request)
    {
        $projects = $this->getFilteredProjects($request);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="project_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($projects) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'S.No',
                'Project Name',
                'Client Name',
                'Start Date',
                'End Date',
                'Status',
                'Team Members'
            ]);

            // Add data rows
            foreach ($projects as $index => $project) {
                // Get team member names for CSV
                $teamIds = explode(',', $project->team);
                $teamMembers = DB::table('allemployees')
                    ->whereIn('id', $teamIds)
                    ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                    ->pluck('name')
                    ->toArray();

                // Format team members for CSV (plain text without HTML)
                $teamText = '';
                if (!empty($teamMembers)) {
                    foreach ($teamMembers as $memberIndex => $name) {
                        $teamText .= ($memberIndex + 1) . '. ' . $name;
                        if ($memberIndex < count($teamMembers) - 1) {
                            $teamText .= ', ';
                        }
                    }
                } else {
                    $teamText = 'N/A';
                }

                fputcsv($file, [
                    $index + 1,
                    $project->projectname ?? '',
                    $project->client ?? '',
                    $project->startdate ? date('d-m-Y', strtotime($project->startdate)) : '',
                    $project->enddate ? date('d-m-Y', strtotime($project->enddate)) : '',
                    $project->status ? ucfirst($project->status) : '',
                    $teamText
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPDF(Request $request)
    {
        // Use filtered projects instead of all projects
        $projects = $this->getFilteredProjects($request);

        // Add team member names
        foreach ($projects as $project) {
            $teamIds = explode(',', $project->team);
            $teamMembers = DB::table('allemployees')
                ->whereIn('id', $teamIds)
                ->select(DB::raw("CONCAT(firstname, ' ', lastname) as name"))
                ->pluck('name')
                ->toArray();

            // Format as numbered list
            if (!empty($teamMembers)) {
                $formattedNames = '';
                foreach ($teamMembers as $index => $name) {
                    $formattedNames .= ($index + 1) . '. ' . $name . '<br>';
                }
                $project->teamNames = $formattedNames;
            } else {
                $project->teamNames = 'N/A';
            }
        }

        $data = [
            'title' => 'Project Report - ' . date('d-m-Y'),
            'projects' => $projects,
            'filters' => [
                'project_name' => $request->project_name,
                'status' => $request->status
            ]
        ];

        $pdf = PDF::loadView('hrms.hr.Reports.projects-reports.pdf', $data);

        return $pdf->download('project_report_' . date('d-m-Y') . '.pdf');
    }

    private function getFilteredProjects(Request $request)
    {
        $query = DB::table('projects');

        // Check your actual client column name - it might be 'client' or you might need a join
        // If projects table has a 'client' column directly:
        if (Schema::hasColumn('projects', 'client')) {
            $query->select('projects.*');
        } else {
            // If you need to join with clients table, adjust the join condition
            // Based on your original code, you were joining on projects.id = clients.id
            // This might be wrong - you should join on client_id foreign key
            $query->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                  ->select(
                      'projects.*',
                      DB::raw("CONCAT(clients.first_name, ' ', clients.last_name) as client")
                  );
        }

        if ($request->has('project_name') && $request->project_name != '') {
            $query->where('projects.projectname', $request->project_name);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('projects.status', $request->status);
        }

        return $query->get();
    }
}