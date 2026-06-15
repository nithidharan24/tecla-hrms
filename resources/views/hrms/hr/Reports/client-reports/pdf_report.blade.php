<!DOCTYPE html>
<html>
<head>
    <title>Client Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-size: 10px;
            text-transform: capitalize;
        }
        .status-active { background-color: #28a745; } /* Green */
        .status-pending { background-color: #ffc107; } /* Yellow */
        .status-inactive { background-color: #dc3545; } /* Red */
        .service-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            background-color: #17a2b8; /* Info blue */
            color: white;
            font-size: 9px;
            margin-right: 3px;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <h1>Client Report</h1>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Client ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Services</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $key => $client)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $client->client_id }}</td>
                <td>{{ $client->first_name }}</td>
                <td>{{ $client->last_name }}</td>
                <td>{{ $client->company_name }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ $client->phone }}</td>
                <td>
                    <span class="status-badge status-{{ $client->status }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </td>
                <td>
                    @php
                        $clientServiceIds = json_decode($client->services ?? '[]', true);
                        $displayedServices = [];
                        if (is_array($clientServiceIds)) {
                            foreach ($clientServiceIds as $serviceId) {
                                if (isset($servicesMap[$serviceId])) {
                                    $displayedServices[] = $servicesMap[$serviceId];
                                }
                            }
                        }
                    @endphp
                    @foreach($displayedServices as $serviceName)
                        <span class="service-badge">{{ $serviceName }}</span>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
