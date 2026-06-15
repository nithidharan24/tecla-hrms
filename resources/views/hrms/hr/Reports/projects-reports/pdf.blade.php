<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
        .filter-info { margin-bottom: 20px; }
        .status-active { color: green; }
        .status-inactive { color: red; }
        .status-pending { color: orange; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>

    @if($filters['project_name'] || $filters['status'])
    <div class="filter-info">
        <h3>Filters Applied:</h3>
        <ul>
            @if($filters['project_name'])
                <li>Project Name: {{ $filters['project_name'] }}</li>
            @endif
            @if($filters['status'])
                <li>Status: {{ ucfirst($filters['status']) }}</li>
            @endif
        </ul>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Project Name</th>
                <th>Client Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Team Members</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $index => $project)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $project->projectname }}</td>
                <td>{{ $project->client }}</td>
                <td>{{ date('d-m-Y', strtotime($project->startdate)) }}</td>
                <td>{{ date('d-m-Y', strtotime($project->enddate)) }}</td>
                <td class="status-{{ $project->status }}">
                    {{ ucfirst($project->status) }}
                </td>
                <td>{!! $project->teamNames !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>