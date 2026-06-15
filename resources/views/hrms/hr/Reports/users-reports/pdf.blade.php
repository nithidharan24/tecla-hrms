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
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-admin { background-color: #dc3545; }
        .badge-employee { background-color: #28a745; }
        .badge-client { background-color: #17a2b8; }
        .badge-default { background-color: #6c757d; }
        .status-active { color: #28a745; }
        .status-inactive { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>

    <div class="filter-info">
        <h3>Filter Applied: {{ $filter }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Designation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @switch($user->role)
                        @case('Admin')<span class="badge badge-admin">{{ $user->role }}</span>@break
                        @case('Employee')<span class="badge badge-employee">{{ $user->role }}</span>@break
                        @case('Client')<span class="badge badge-client">{{ $user->role }}</span>@break
                        @default<span class="badge badge-default">{{ $user->role }}</span>
                    @endswitch
                </td>
                <td class="status-{{ strtolower($user->status) }}">
                    {{ ucfirst($user->status) }}
                </td>
                <td>{{ $user->designation ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>