<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    
    @if($filters['project'])
        <p><strong>Project:</strong> {{ $filters['project'] }}</p>
    @endif
    @if($filters['status'])
        <p><strong>Status:</strong> {{ $filters['status'] }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Task Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th class="text-center">Status</th>
                <th>Assigned To</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $index => $task)
            <tr>
                <td>{{ $index + 1 }}</td>
<td>{{ $task->task }}</td>                <td>
                    @if(isset($task->start_date) && $task->start_date)
                        {{ date('d-m-Y', strtotime($task->start_date)) }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if(isset($task->end_date) && $task->end_date)
                        {{ date('d-m-Y', strtotime($task->end_date)) }}
                    @else
                        N/A
                    @endif
                </td>
<td class="text-center">
    {{ ucfirst($task->status) }}
</td>
                <td>{{ $task->assigned_to_name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>