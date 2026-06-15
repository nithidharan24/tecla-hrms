<?php

namespace App\Http\Controllers\Backend\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Import the PDF facade

class ClientreportController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('clients')->where('deleted_at', 0);

        // Apply filters
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('client_name')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->input('client_name') . '%')
                  ->orWhere('last_name', 'like', '%' . $request->input('client_name') . '%');
            });
        }

        if ($request->filled('company_name')) {
            $query->where('company_name', $request->input('company_name'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $clients = $query->get();

        $companyNames = DB::table('clients')
            ->where('deleted_at', 0)
            ->pluck('company_name', 'company_name');

        // Fetch all services and create a map for easy lookup
        $servicesMap = DB::table('services')->pluck('name', 'id')->all();

        return view('hrms.hr.Reports.client-reports.index', compact('clients', 'companyNames', 'servicesMap'));
    }

    public function exportCsv(Request $request)
    {
        $query = DB::table('clients')->where('deleted_at', 0);

        // Apply filters
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('client_name')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->input('client_name') . '%')
                  ->orWhere('last_name', 'like', '%' . $request->input('client_name') . '%');
            });
        }

        if ($request->filled('company_name')) {
            $query->where('company_name', $request->input('company_name'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $clients = $query->get();

        // Fetch all services and create a map for easy lookup
        $servicesMap = DB::table('services')->pluck('name', 'id')->all();

        $filename = 'client_reports_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($clients, $servicesMap) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['S.No', 'Client ID', 'First Name', 'Last Name', 'Company Name', 'Email', 'Phone', 'Status', 'Services']); // CSV Headers

            foreach ($clients as $key => $client) {
                $clientServiceIds = json_decode($client->services, true);
                $serviceNames = [];
                if (is_array($clientServiceIds)) {
                    foreach ($clientServiceIds as $serviceId) {
                        if (isset($servicesMap[$serviceId])) {
                            $serviceNames[] = $servicesMap[$serviceId];
                        }
                    }
                }
                $serviceNamesString = implode(', ', $serviceNames);

                fputcsv($file, [
                    $key + 1,
                    $client->client_id,
                    $client->first_name,
                    $client->last_name,
                    $client->company_name,
                    $client->email,
                    $client->phone,
                    ucfirst($client->status),
                    $serviceNamesString
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = DB::table('clients')->where('deleted_at', 0);

        // Apply filters (same as index and csv export)
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('client_name')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->input('client_name') . '%')
                  ->orWhere('last_name', 'like', '%' . $request->input('client_name') . '%');
            });
        }

        if ($request->filled('company_name')) {
            $query->where('company_name', $request->input('company_name'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $clients = $query->get();

        // Fetch all services and create a map for easy lookup
        $servicesMap = DB::table('services')->pluck('name', 'id')->all();

        $pdf = Pdf::loadView('hrms.hr.Reports.client-reports.pdf_report', compact('clients', 'servicesMap'));
        return $pdf->download('client_reports_' . date('Ymd_His') . '.pdf');
    }
}
