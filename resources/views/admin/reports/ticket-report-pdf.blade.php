<!DOCTYPE html>
<html>
<head>
    <title>Ticket Report</title>

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

    <h2>Ticketing Activity Report</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Assignee</th>
                <th>SLA</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($tickets as $ticket)

                @php
                    $isOverdue =
                        $ticket->due_at &&
                        now()->greaterThan($ticket->due_at) &&
                        !in_array($ticket->status, ['resolved', 'closed']);
                @endphp

                <tr>
                    <td>{{ $ticket->ticket_id }}</td>
                    <td>{{ $ticket->subject }}</td>

                    <td>
                        {{ optional($ticket->category)->name }}
                    </td>

                    <td>
                        {{ optional($ticket->technician)->name ?? 'Unassigned' }}
                    </td>

                    <td>
                        {{ $isOverdue ? 'Overdue' : 'Within SLA' }}
                    </td>

                    <td>
                        {{ ucfirst($ticket->status) }}
                    </td>
                </tr>

            @endforeach
        </tbody>
    </table>

</body>
</html>
