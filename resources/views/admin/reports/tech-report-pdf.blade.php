<!DOCTYPE html>
<html>
<head>
    <title>Technician Performance Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        table th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

    <h2>Technician Performance Report</h2>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Assigned</th>
                <th>In Progress</th>
                <th>Closed</th>
            </tr>
        </thead>

        <tbody>
            @foreach($technicians as $tech)
                <tr>
                    <td>{{ $tech->name }}</td>
                    <td>{{ $tech->assigned_count }}</td>
                    <td>{{ $tech->inprogress_count }}</td>
                    <td>{{ $tech->closed_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
