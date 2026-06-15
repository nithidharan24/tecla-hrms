<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        h3, p {
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h3>{{ $title }}</h3>
<p>Generated at: {{ $generated_at }}</p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Sub Category</th>
            <th>Amount</th>
            <th>Currency</th>
            <th>Notes</th>
            <th>Expense Date</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->category_name ?? 'N/A' }}</td>
            <td>{{ $row->subcategory_name ?? 'N/A' }}</td>
            <td>{{ $row->currency_symbol }}{{ number_format($row->amount, 2) }}</td>
            <td>{{ $row->currency_symbol }}</td>
            <td>{{ $row->Notes }}</td>
            <td>{{ $row->expense_date ? date('d/m/Y', strtotime($row->expense_date)) : '' }}</td>
            <td>{{ $row->created_at ? date('d/m/Y H:i', strtotime($row->created_at)) : '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
