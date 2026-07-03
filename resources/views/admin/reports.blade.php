<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reports - Resolve</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/dist/css/style.css">
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        :root {
            --primary: #3B82F6;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --purple: #8B5CF6;
            --open: #0EA5E9;
            --rpt-border: #E2E8F0;
        }

        /* ── Tab Strip ─────────────────────────── */
        .dent-tabs {
            display: flex;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            background-color: #f3f4f6;
            width: fit-content;
        }
        .dent-tab {
            padding: 0.7rem 2.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.25s ease;
            text-align: center;
            user-select: none;
            border: none;
            background: none;
            outline: none;
        }
        .dent-tab:hover {
            color: #374151;
        }
        .dent-tab.active {
            background-color: #FBBF24;
            color: #ffffff;
        }

        /* ── Tab panes ─────────────────────────── */
        .tab-pane { display: none; border: none; outline: none; box-shadow: none; }
        .tab-pane.active {
            display: block;
            animation: fadeIn 0.25s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Filter bar ────────────────────────── */
        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 3px; }
        .filter-group label { font-size: 0.6875rem; font-weight: 600; color: #64748B; text-transform: uppercase; }
        .filter-select {
            padding: 7px 12px;
            border: 1px solid var(--rpt-border);
            border-radius: 7px;
            font-size: 0.8rem;
            color: #334155;
            background: #fff;
            cursor: pointer;
            outline: none;
            min-width: 150px;
        }
        .filter-date-input {
            padding: 7px 10px;
            border: 1px solid var(--rpt-border);
            border-radius: 7px;
            font-size: 0.8rem;
            color: #334155;
            background: #fff;
            outline: none;
        }
        .date-picker-group { display: none; flex-direction: column; gap: 3px; }
        .date-picker-group.visible { display: flex; }
        .apply-btn {
            background: #1E293B;
            color: #fff !important;
            border: none;
            border-radius: 7px;
            padding: 8px 16px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* ── Section Label ─────────────────────── */
        .section-label {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            margin-bottom: 12px;
        }

        /* ── KPI Cards ─────────────────────────── */
        .kpi-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }
        .kpi-card {
            background: #fff;
            border: 1px solid var(--rpt-border);
            border-radius: 10px;
            padding: 16px 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: #FBBF24;
        }
        .kpi-card .kpi-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .kpi-card .kpi-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1E293B;
            margin-top: 6px;
            line-height: 1;
        }

        /* ── Chart Grid layouts ────────────────── */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .grid-1-2 {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .grid-2-1 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        /* ── Card ──────────────────────────────── */
        .chart-card {
            background: #fff;
            border: 1px solid var(--rpt-border);
            border-radius: 10px;
            padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-bottom: 16px;
        }
        .chart-card h3 {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #1E293B;
            margin: 0 0 14px;
        }

        /* ── Table ─────────────────────────────── */
        .table-card {
            background: #fff;
            border: 1px solid var(--rpt-border);
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            overflow-x: auto;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .table-card h3 {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #1E293B;
            margin: 0;
        }
        .export-dropdown {
            position: relative;
            display: inline-block;
        }
        .export-btn-sm {
            background: #F8FAFC;
            color: #334155;
            border: 1px solid var(--rpt-border);
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .export-btn-sm:hover {
            background: #F1F5F9;
            border-color: #CBD5E1;
        }
        .export-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 120px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1);
            z-index: 10;
            border-radius: 6px;
            border: 1px solid var(--rpt-border);
            overflow: hidden;
            margin-top: 4px;
        }
        .export-dropdown-content a {
            color: #334155;
            padding: 8px 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            font-weight: 500;
            transition: background 0.2s;
            cursor: pointer;
        }
        .export-dropdown-content a:hover {
            background-color: #F8FAFC;
            color: var(--primary);
        }
        .export-dropdown-content.show {
            display: block;
        }
        table.reports-table { width: 100%; border-collapse: collapse; min-width: 600px; }
        table.reports-table th {
            text-align: left;
            font-size: 0.6875rem;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            padding: 10px 12px;
            border-bottom: 1px solid var(--rpt-border);
            background: #F8FAFC;
        }
        table.reports-table td {
            font-size: 0.8rem;
            color: #334155;
            padding: 11px 12px;
            border-bottom: 1px solid #F1F5F9;
        }
        table.reports-table tr:last-child td { border-bottom: none; }
        table.reports-table tr:hover td { background: #F8FAFC; }

        .badge-open     { background:#EFF6FF; color:#1D4ED8; padding:2px 8px; border-radius:12px; font-size:0.7rem; font-weight:600; }
        .badge-overdue  { background:#FEF2F2; color:#B91C1C; padding:2px 8px; border-radius:12px; font-size:0.7rem; font-weight:600; }
        .badge-closed   { background:#ECFDF5; color:#065F46; padding:2px 8px; border-radius:12px; font-size:0.7rem; font-weight:600; }

        /* ── Period Toggle ─────────────────────── */
        .period-toggles { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; }
        .period-toggles button {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--rpt-border);
            background: #fff;
            color: #64748B;
        }
        .period-toggles button.active {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
        }

        /* ── Responsive ────────────────────────── */
        @media (max-width: 900px) {
            .grid-2, .grid-1-2, .grid-2-1 { grid-template-columns: 1fr; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-group { width: 100%; }
            .filter-select, .filter-date-input { width: 100%; }
            .apply-btn { width: 100%; justify-content: center; }
            .kpi-container { grid-template-columns: repeat(2, 1fr); }
            .dent-tabs { flex-direction: column; width: 100%; }
            .table-header { flex-direction: column; align-items: flex-start; }
            .export-dropdown { width: 100%; }
            .export-btn-sm { width: 100%; justify-content: center; }
            .export-dropdown-content { width: 100%; position: static; box-shadow: none; border-top: none; }
        }
        @media (max-width: 480px) {
            .kpi-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    @include('admin.partials.sidebar')

    <main class="main-content">
        {{-- ── Standard Top Header (matches all other admin pages) ── --}}
        <header class="top-header">
            <button class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <form method="GET" action="{{ route('admin.reports') }}" style="flex:1; max-width:500px; margin:0 1.5rem;">
                <input type="hidden" name="active_tab" value="{{ request('active_tab', 'company') }}">
                <div class="search-container" style="width:100%; margin:0;">
                    <button type="submit" style="background:none; border:none; color:inherit; cursor:pointer; padding:0; margin-right:8px;"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Ticket ID, Subject, Description, or User..." />
                </div>
            </form>
            <div class="header-actions">
                <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                    <i class="fa-regular fa-circle-user"></i>
                </a>
            </div>
        </header>

        <div class="dashboard-content">
            {{-- Breadcrumb --}}
            <div class="breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a> &gt;
                <span>Reports</span>
            </div>
            <div class="page-header-title">
                <div>
                    <h1>Reports</h1>
                </div>
                <div style="font-size:0.8rem; color:#64748B;">
                </div>
            </div>

            {{-- ── Tab Buttons ── --}}
            <div class="dent-tabs">
                <button id="tab-btn-company" class="dent-tab active" onclick="switchTab('company', this)">Company / Department</button>
                <button id="tab-btn-staff"   class="dent-tab" onclick="switchTab('staff', this)">Staff</button>
                <button id="tab-btn-period"  class="dent-tab" onclick="switchTab('period', this)">Period</button>
            </div>

            {{-- ══════════════════════════════════════════
                 TAB 1 : Company / Department
            ══════════════════════════════════════════ --}}
            <div id="company-tab" class="tab-pane active">

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('admin.reports') }}" id="companyFilterForm">
                    <input type="hidden" name="active_tab" value="company">
                    <div class="filter-bar">

                        {{-- Company --}}
                        <div class="filter-group">
                            <label>Company</label>
                            <select name="company_id" id="cmp-company-select" class="filter-select" onchange="filterDepts('cmp', this.value); this.form.submit()">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Department --}}
                        <div class="filter-group">
                            <label>Department</label>
                            <select name="department_id" id="cmp-dept-select" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Select Type of Report --}}
                        <div class="filter-group">
                            <label>Select Type of Report</label>
                            <select name="report_type" class="filter-select" id="cmp-report-type" onchange="toggleDatePicker('cmp', this.value)">
                                <option value="">-- Select --</option>
                                <option value="daily"   {{ request('report_type') == 'daily'   ? 'selected' : '' }}>Daily</option>
                                <option value="weekly"  {{ request('report_type') == 'weekly'  ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="custom"  {{ request('report_type') == 'custom'  ? 'selected' : '' }}>Date Range</option>
                            </select>
                        </div>

                        {{-- Daily date --}}
                        <div class="date-picker-group {{ request('report_type') == 'daily' ? 'visible' : '' }}" id="cmp-daily-group">
                            <label>Select Date</label>
                            <input type="date" name="daily_date" class="filter-date-input" value="{{ request('daily_date') }}">
                        </div>
                        {{-- Weekly date --}}
                        <div class="date-picker-group {{ request('report_type') == 'weekly' ? 'visible' : '' }}" id="cmp-weekly-group">
                            <label>Select Week</label>
                            <input type="week" name="weekly_date" class="filter-date-input" value="{{ request('weekly_date') }}">
                        </div>
                        {{-- Monthly date --}}
                        <div class="date-picker-group {{ request('report_type') == 'monthly' ? 'visible' : '' }}" id="cmp-monthly-group">
                            <label>Select Month</label>
                            <input type="month" name="monthly_date" class="filter-date-input" value="{{ request('monthly_date') }}">
                        </div>
                        {{-- Date Range --}}
                        <div class="date-picker-group {{ request('report_type') == 'custom' ? 'visible' : '' }}" id="cmp-custom-group">
                            <label>From</label>
                            <input type="date" name="start_date" class="filter-date-input" value="{{ request('start_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'custom' ? 'visible' : '' }}" id="cmp-custom-group2">
                            <label>To</label>
                            <input type="date" name="end_date" class="filter-date-input" value="{{ request('end_date') }}">
                        </div>

                        <button type="submit" class="apply-btn" style="margin-bottom:0; align-self:flex-end;">Apply</button>
                        @if(request()->hasAny(['company_id','department_id','report_type','daily_date','weekly_date','monthly_date','start_date','end_date']))
                            <a href="{{ route('admin.reports') }}" class="apply-btn" style="background:#64748B; text-decoration:none; align-self:flex-end; display:inline-flex; align-items:center; gap:6px;">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>

                {{-- KPI Cards --}}
                <div class="section-label">Key Performance Indicators</div>
                <div class="kpi-container">
                    <div class="kpi-card" style="--kpi-color: var(--primary)">
                        <div class="kpi-label">Total Tickets</div>
                        <div class="kpi-value">{{ number_format($totalTickets) }}</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--open)">
                        <div class="kpi-label">Open Tickets</div>
                        <div class="kpi-value">{{ number_format($openTickets) }}</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--success)">
                        <div class="kpi-label">Closed Tickets</div>
                        <div class="kpi-value">{{ number_format($closedTickets) }}</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--warning)">
                        <div class="kpi-label">Avg Resolution</div>
                        <div class="kpi-value">{{ $avgResolutionDays }}<span style="font-size:0.875rem;"> days</span></div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--purple)">
                        <div class="kpi-label">SLA Compliance</div>
                        <div class="kpi-value">{{ $slaCompliance !== null ? $slaCompliance . '%' : 'N/A' }}</div>
                    </div>
                </div>

                {{-- Charts Row 1: Donut + Dept Bar --}}
                <div class="grid-1-2">
                    <div class="chart-card" style="margin-bottom:0;">
                        <h3>Ticket Status Distribution</h3>
                        <div style="position:relative; height:230px;">
                            <canvas id="statusDonutChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card" style="margin-bottom:0;">
                        <h3>Tickets by Department</h3>
                        <div style="position:relative; height:230px;">
                            <canvas id="deptBarChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Charts Row 2: Monthly Trend --}}
                <div class="chart-card">
                    <h3>Monthly Ticket Trend</h3>
                    <div style="position:relative; height:220px;">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>

                {{-- Department Summary Table --}}
                <div class="table-card">
                    <div class="table-header">
                        <h3>Department Summary Table</h3>
                        <div class="export-dropdown">
                            <button class="export-btn-sm" onclick="toggleExportMenu('dept-export-menu', event)"><i class="fa-solid fa-download"></i> Export <i class="fa-solid fa-chevron-down" style="font-size: 0.6rem; margin-left: 4px;"></i></button>
                            <div class="export-dropdown-content" id="dept-export-menu">
                                <a onclick="exportPDF('dept-table', 'Department Summary Table')"><i class="fa-solid fa-file-pdf" style="color: #EF4444;"></i> PDF</a>
                                <a onclick="exportExcel('dept-table', 'Department Summary')"><i class="fa-solid fa-file-excel" style="color: #10B981;"></i> Excel</a>
                            </div>
                        </div>
                    </div>
                    <table class="reports-table" id="dept-table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Total Tickets</th>
                                <th>Closed</th>
                                <th>Open</th>
                                <th>Overdue</th>
                                <th>Avg Resolution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deptTable as $row)
                            <tr>
                                <td><strong>{{ $row['dept'] }}</strong></td>
                                <td>{{ $row['total'] }}</td>
                                <td><span class="badge-closed">{{ $row['closed'] }}</span></td>
                                <td><span class="badge-open">{{ $row['open'] }}</span></td>
                                <td><span class="badge-overdue">{{ $row['overdue'] }}</span></td>
                                <td>{{ $row['avg'] }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" style="text-align:center; color:#94A3B8; padding:30px;">No data for selected filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>{{-- /company-tab --}}


            {{-- ══════════════════════════════════════════
                 TAB 2 : Staff
            ══════════════════════════════════════════ --}}
            <div id="staff-tab" class="tab-pane">

                <form method="GET" action="{{ route('admin.reports') }}" id="staffFilterForm">
                    <input type="hidden" name="active_tab" value="staff">
                    <div class="filter-bar">

                        <div class="filter-group">
                            <label>Company</label>
                            <select name="company_id" id="stf-company-select" class="filter-select" onchange="filterDepts('stf', this.value); this.form.submit()">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Department</label>
                            <select name="department_id" id="stf-dept-select" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" data-company="{{ $dept->company_id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Technician</label>
                            <select name="technician_id" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Technicians</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Select Type of Report</label>
                            <select name="report_type" class="filter-select" id="stf-report-type" onchange="toggleDatePicker('stf', this.value)">
                                <option value="">-- Select --</option>
                                <option value="daily"   {{ request('report_type') == 'daily'   ? 'selected' : '' }}>Daily</option>
                                <option value="weekly"  {{ request('report_type') == 'weekly'  ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="custom"  {{ request('report_type') == 'custom'  ? 'selected' : '' }}>Date Range</option>
                            </select>
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'daily' ? 'visible' : '' }}" id="stf-daily-group">
                            <label>Select Date</label>
                            <input type="date" name="daily_date" class="filter-date-input" value="{{ request('daily_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'weekly' ? 'visible' : '' }}" id="stf-weekly-group">
                            <label>Select Week</label>
                            <input type="week" name="weekly_date" class="filter-date-input" value="{{ request('weekly_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'monthly' ? 'visible' : '' }}" id="stf-monthly-group">
                            <label>Select Month</label>
                            <input type="month" name="monthly_date" class="filter-date-input" value="{{ request('monthly_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'custom' ? 'visible' : '' }}" id="stf-custom-group">
                            <label>From</label>
                            <input type="date" name="start_date" class="filter-date-input" value="{{ request('start_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'custom' ? 'visible' : '' }}" id="stf-custom-group2">
                            <label>To</label>
                            <input type="date" name="end_date" class="filter-date-input" value="{{ request('end_date') }}">
                        </div>

                        <button type="submit" class="apply-btn" style="align-self:flex-end;  color: #ffff !important;">Apply</button>
                        @if(request()->hasAny(['company_id','department_id','technician_id','report_type','daily_date','weekly_date','monthly_date','start_date','end_date']))
                            <a href="{{ route('admin.reports') }}?active_tab=staff" class="apply-btn" style="background:#111111; text-decoration:none; align-self:flex-end; display:inline-flex; align-items:center; gap:6px;">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>

                <div class="section-label">Key Performance Indicators</div>
                <div class="kpi-container">
                    <div class="kpi-card" style="--kpi-color: var(--primary)">
                        <div class="kpi-label">Total Technicians</div>
                        <div class="kpi-value">{{ $totalTechnicians }}</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--success)">
                        <div class="kpi-label">Overall SLA Compliance</div>
                        <div class="kpi-value">{{ $overallSlaCompliance !== null ? $overallSlaCompliance . '%' : 'N/A' }}</div>
                    </div>
                    <!-- <div class="kpi-card" style="--kpi-color: var(--warning)">
                        <div class="kpi-label">Tickets Assigned</div>
                        <div class="kpi-value">{{ number_format($ticketsAssigned) }}</div>
                    </div> -->
                    <div class="kpi-card" style="--kpi-color: #EF4444">
                        <div class="kpi-label">Tickets Overdue</div>
                        <div class="kpi-value">{{ number_format($ticketsOverdue) }}</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--purple)">
                        <div class="kpi-label">Tickets Closed</div>
                        <div class="kpi-value">{{ number_format($ticketsClosed) }}</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--open)">
                        <div class="kpi-label">Avg Resolution</div>
                        <div class="kpi-value">{{ $avgResolutionDays }}<span style="font-size:0.875rem;"> days</span></div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="chart-card" style="margin-bottom:0;">
                        <h3>Technician Current Workload (Active Tickets)</h3>
                        <div style="position:relative; height:230px;">
                            <canvas id="staffWorkloadChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card" style="margin-bottom:0;">
                        <h3>Technician Performance (Closed)</h3>
                        <div style="position:relative; height:230px;">
                            <canvas id="staffClosedChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid-2-1">
                    <div class="chart-card" style="margin-bottom:0;">
                        <h3>Avg Resolution Time per Technician (Days)</h3>
                        <div style="position:relative; height:230px;">
                            <canvas id="staffResTimeChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card" style="margin-bottom:0;">
                        <h3>Staff Ticket Status</h3>
                        <div style="position:relative; height:230px;">
                            <canvas id="staffDonutChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-header">
                        <h3>Technician Summary Table</h3>
                        <div class="export-dropdown">
                            <button class="export-btn-sm" onclick="toggleExportMenu('staff-export-menu', event)"><i class="fa-solid fa-download"></i> Export <i class="fa-solid fa-chevron-down" style="font-size: 0.6rem; margin-left: 4px;"></i></button>
                            <div class="export-dropdown-content" id="staff-export-menu">
                                <a onclick="exportPDF('staff-table', 'Technician Summary Table')"><i class="fa-solid fa-file-pdf" style="color: #EF4444;"></i> PDF</a>
                                <a onclick="exportExcel('staff-table', 'Technician Summary')"><i class="fa-solid fa-file-excel" style="color: #10B981;"></i> Excel</a>
                            </div>
                        </div>
                    </div>
                    <table class="reports-table" id="staff-table">
                        <thead>
                            <tr>
                                <th>Technician</th>
                                <th>Assigned</th>
                                <th>Completed</th>
                                <th>Open</th>
                                <th>Overdue</th>
                                <th>Avg Resolution</th>
                                <th>SLA Met</th>
                                <th>SLA Breached</th>
                                <th>SLA Compliance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffTable as $row)
                            <tr>
                                <td><strong>{{ $row['name'] }}</strong></td>
                                <td>{{ $row['assigned'] }}</td>
                                <td><span class="badge-closed">{{ $row['closed'] }}</span></td>
                                <td><span class="badge-open">{{ $row['open'] }}</span></td>
                                <td><span class="badge-overdue">{{ $row['overdue'] }}</span></td>
                                <td>{{ $row['avg'] }}</td>
                                <td><span style="color:#059669; font-weight:600;">{{ $row['sla_met'] }}</span></td>
                                <td><span style="color:#dc2626; font-weight:600;">{{ $row['sla_breached'] }}</span></td>
                                <td>
                                    @if($row['sla_pct'] === 'N/A')
                                        <span style="color:#94A3B8;">N/A</span>
                                    @else
                                        <span style="font-weight:700; color: {{ (int)$row['sla_pct'] >= 80 ? '#059669' : ((int)$row['sla_pct'] >= 50 ? '#D97706' : '#dc2626') }};">
                                            {{ $row['sla_pct'] }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" style="text-align:center; color:#94A3B8; padding:30px;">No data for selected filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>{{-- /staff-tab --}}


            {{-- ══════════════════════════════════════════
                 TAB 3 : Period
            ══════════════════════════════════════════ --}}
            <div id="period-tab" class="tab-pane">

                <form method="GET" action="{{ route('admin.reports') }}" id="periodFilterForm">
                    <input type="hidden" name="active_tab" value="period">
                    <div class="filter-bar">
                        <div class="filter-group">
                            <label>Select Type of Report</label>
                            <select name="report_type" class="filter-select" id="prd-report-type" onchange="toggleDatePicker('prd', this.value)">
                                <option value="">-- Select --</option>
                                <option value="daily"   {{ request('report_type') == 'daily'   ? 'selected' : '' }}>Daily</option>
                                <option value="weekly"  {{ request('report_type') == 'weekly'  ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="custom"  {{ request('report_type') == 'custom'  ? 'selected' : '' }}>Date Range</option>
                            </select>
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'daily' ? 'visible' : '' }}" id="prd-daily-group">
                            <label>Select Date</label>
                            <input type="date" name="daily_date" class="filter-date-input" value="{{ request('daily_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'weekly' ? 'visible' : '' }}" id="prd-weekly-group">
                            <label>Select Week</label>
                            <input type="week" name="weekly_date" class="filter-date-input" value="{{ request('weekly_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'monthly' ? 'visible' : '' }}" id="prd-monthly-group">
                            <label>Select Month</label>
                            <input type="month" name="monthly_date" class="filter-date-input" value="{{ request('monthly_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'custom' ? 'visible' : '' }}" id="prd-custom-group">
                            <label>From</label>
                            <input type="date" name="start_date" class="filter-date-input" value="{{ request('start_date') }}">
                        </div>
                        <div class="date-picker-group {{ request('report_type') == 'custom' ? 'visible' : '' }}" id="prd-custom-group2">
                            <label>To</label>
                            <input type="date" name="end_date" class="filter-date-input" value="{{ request('end_date') }}">
                        </div>

                        <button type="submit" class="apply-btn" style="align-self:flex-end;">Apply</button>
                        @if(request()->hasAny(['report_type','daily_date','weekly_date','monthly_date','start_date','end_date']))
                            <a href="{{ route('admin.reports') }}?active_tab=period" class="apply-btn" style="background:#64748B; text-decoration:none; align-self:flex-end; display:inline-flex; align-items:center; gap:6px;">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>

                <div class="section-label">Key Performance Indicators</div>
                <div class="kpi-container">
                    <div class="kpi-card" style="--kpi-color: var(--primary)">
                        <div class="kpi-label">Total Tickets</div>
                        <div class="kpi-value">{{ number_format($totalTickets) }}</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--success)">
                        <div class="kpi-label">Closed Tickets</div>
                        <div class="kpi-value">{{ number_format($closedTickets) }}</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--purple)">
                        <div class="kpi-label">Resolution Rate</div>
                        <div class="kpi-value">{{ $totalTickets > 0 ? round(($completedTickets / $totalTickets) * 100, 1) : 0 }}%</div>
                    </div>
                    <div class="kpi-card" style="--kpi-color: var(--warning)">
                        <div class="kpi-label">SLA Compliance</div>
                        <div class="kpi-value">{{ $slaCompliance !== null ? $slaCompliance . '%' : 'N/A' }}</div>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Ticket Creation Trend</h3>
                    <div style="position:relative; height:220px;">
                        <canvas id="creationTrendChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Open vs Closed Tickets per Month</h3>
                    <div style="position:relative; height:230px;">
                        <canvas id="openClosedChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Ticket Categories Trend</h3>
                    <div style="position:relative; height:240px;">
                        <canvas id="categoryTrendChart"></canvas>
                    </div>
                </div>
            </div>{{-- /period-tab --}}

        </div>{{-- /dashboard-content --}}
    </main>
</div>

{{-- ── JavaScript ── --}}
<script>

// ── Tab Switching ──────────────────────────────
function switchTab(tabId, btn) {
    document.querySelectorAll('.dent-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(tabId + '-tab').classList.add('active');
    // destroy & redraw charts for the newly active tab
    drawChartsForTab(tabId);
}

// ── Export Menu Toggling ───────────────────────
function toggleExportMenu(menuId, event) {
    event.stopPropagation();
    const menu = document.getElementById(menuId);
    const isShowing = menu.classList.contains('show');
    // Close all open menus first
    document.querySelectorAll('.export-dropdown-content').forEach(m => m.classList.remove('show'));
    if (!isShowing) {
        menu.classList.add('show');
    }
}
window.addEventListener('click', function(e) {
    if (!e.target.closest('.export-dropdown')) {
        document.querySelectorAll('.export-dropdown-content').forEach(m => m.classList.remove('show'));
    }
});

// ── Date Picker Toggling ───────────────────────
function toggleDatePicker(prefix, type) {
    const groups = ['daily-group','weekly-group','monthly-group','custom-group','custom-group2'];
    groups.forEach(g => {
        const el = document.getElementById(prefix + '-' + g);
        if (el) el.classList.remove('visible');
    });
    if (type === 'daily') {
        const el = document.getElementById(prefix + '-daily-group');
        if (el) el.classList.add('visible');
    } else if (type === 'weekly') {
        const el = document.getElementById(prefix + '-weekly-group');
        if (el) el.classList.add('visible');
    } else if (type === 'monthly') {
        const el = document.getElementById(prefix + '-monthly-group');
        if (el) el.classList.add('visible');
    } else if (type === 'custom') {
        const el1 = document.getElementById(prefix + '-custom-group');
        const el2 = document.getElementById(prefix + '-custom-group2');
        if (el1) el1.classList.add('visible');
        if (el2) el2.classList.add('visible');
    }
}

// ── Chart.js Defaults ─────────────────────────
Chart.defaults.font.family = "'Inter','Segoe UI',sans-serif";
Chart.defaults.color = '#64748B';
Chart.defaults.scale.grid.color = '#F1F5F9';

// ── Data from Controller ──────────────────────
const donutData     = @json($donutData);
const deptData      = @json($deptData);
const monthlyTrend  = @json($monthlyTrend);

const staffWorkload = @json($staffWorkload);
const staffClosed   = @json($staffClosed);
const staffResTime  = @json($staffResTime);
const staffDonut    = @json($staffDonut);

const creationTrend    = @json($creationTrend);
const openClosed       = @json($openClosed);
const categoryTrend    = @json($categoryTrend);

// ── Chart registry (destroy before re-draw) ───
const chartRegistry = {};

function destroyChart(id) {
    if (chartRegistry[id]) { chartRegistry[id].destroy(); delete chartRegistry[id]; }
}

// ── Inline percentage plugin for doughnut charts ──
const donutPercentPlugin = {
    id: 'donutPercent',
    afterDraw(chart) {
        if (chart.config.type !== 'doughnut') return;
        const { ctx, data } = chart;
        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
        if (total === 0) return;
        chart.getDatasetMeta(0).data.forEach((arc, i) => {
            const value = data.datasets[0].data[i];
            if (value === 0) return;
            const pct = Math.round((value / total) * 100);
            const angle = (arc.startAngle + arc.endAngle) / 2;
            const radius = (arc.innerRadius + arc.outerRadius) / 2;
            const x = arc.x + Math.cos(angle) * radius;
            const y = arc.y + Math.sin(angle) * radius;
            ctx.save();
            ctx.font = 'bold 11px Inter, Segoe UI, sans-serif';
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.shadowColor = 'rgba(0,0,0,0.4)';
            ctx.shadowBlur = 3;
            ctx.fillText(pct + '%', x, y);
            ctx.restore();
        });
    }
};
// Register plugin globally once
if (!Chart.registry.plugins.get('donutPercent')) {
    Chart.register(donutPercentPlugin);
}

function makeDonut(id, data) {
    destroyChart(id);
    const ctx = document.getElementById(id);
    if (!ctx) return;
    chartRegistry[id] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.name),
            datasets: [{
                data: data.map(d => d.value),
                backgroundColor: data.map(d => d.color),
                borderWidth: 2,
                borderColor: '#fff',
                cutout: '58%'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position:'bottom', labels:{ boxWidth:10, usePointStyle:true, padding:12 } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                            const pct = total > 0 ? Math.round((ctx.parsed/total)*100) : 0;
                            return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });
}

function makeHBar(id, labels, values, color) {
    destroyChart(id);
    const ctx = document.getElementById(id);
    if (!ctx) return;
    chartRegistry[id] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets:[{ data:values, backgroundColor:color, borderRadius:4 }]
        },
        options: {
            indexAxis:'y', responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{ x:{ beginAtZero:true, grid:{ display:false } }, y:{ grid:{ display:false } } }
        }
    });
}

function makeVBar(id, labels, values, color, label) {
    destroyChart(id);
    const ctx = document.getElementById(id);
    if (!ctx) return;
    chartRegistry[id] = new Chart(ctx, {
        type:'bar',
        data:{
            labels,
            datasets:[{ label, data:values, backgroundColor:color, borderRadius:4 }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } }
        }
    });
}

function makeLine(id, labels, values, color, label) {
    destroyChart(id);
    const ctx = document.getElementById(id);
    if (!ctx) return;
    chartRegistry[id] = new Chart(ctx, {
        type:'line',
        data:{
            labels,
            datasets:[{ label, data:values, borderColor:color, backgroundColor:color+'33',
                        borderWidth:2, tension:0.35, pointRadius:4, fill:true }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } }
        }
    });
}

function drawChartsForTab(tab) {
    if (tab === 'company') {
        makeDonut('statusDonutChart', donutData);
        makeVBar('deptBarChart', deptData.map(d=>d.dept), deptData.map(d=>d.tickets), '#028ab6', 'Tickets');
        makeLine('monthlyTrendChart', monthlyTrend.map(d=>d.month), monthlyTrend.map(d=>d.tickets), '#063765', 'Tickets');
    }
    else if (tab === 'staff') {
        makeHBar('staffWorkloadChart', staffWorkload.map(d=>d.name), staffWorkload.map(d=>d.assigned), '#028ab6');
        makeHBar('staffClosedChart', staffClosed.map(d=>d.name), staffClosed.map(d=>d.closed), '#32a249');
        // Resolution time bar — colour based on value
        destroyChart('staffResTimeChart');
        const rtCtx = document.getElementById('staffResTimeChart');
        if (rtCtx) {
            chartRegistry['staffResTimeChart'] = new Chart(rtCtx, {
                type:'bar',
                data:{
                    labels: staffResTime.map(d=>d.name),
                    datasets:[{
                        data: staffResTime.map(d=>d.days),
                        backgroundColor: staffResTime.map(d => d.days > 3.5 ? '#df3846' : d.days > 2 ? '#ffc000' : '#32a249'),
                        borderRadius:4
                    }]
                },
                options:{
                    responsive:true, maintainAspectRatio:false,
                    plugins:{ legend:{ display:false } },
                    scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } }
                }
            });
        }
        makeDonut('staffDonutChart', staffDonut);
    }
    else if (tab === 'period') {
        makeLine('creationTrendChart', creationTrend.map(d=>d.month), creationTrend.map(d=>d.tickets), '#063765', 'Created');

        // Open vs Closed grouped bar
        destroyChart('openClosedChart');
        const ocCtx = document.getElementById('openClosedChart');
        if (ocCtx) {
            chartRegistry['openClosedChart'] = new Chart(ocCtx, {
                type:'bar',
                data:{
                    labels: openClosed.map(d=>d.month),
                    datasets:[
                        { label:'Open',   data:openClosed.map(d=>d.open),   backgroundColor:'#028ab6', borderRadius:4 },
                        { label:'Closed', data:openClosed.map(d=>d.closed), backgroundColor:'#32a249', borderRadius:4 }
                    ]
                },
                options:{
                    responsive:true, maintainAspectRatio:false,
                    plugins:{ legend:{ position:'top', align:'end', labels:{ usePointStyle:true, boxWidth:10 } } },
                    scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } }
                }
            });
        }

        // Category usage bar chart
        if (categoryTrend.length > 0) {
            makeVBar('categoryTrendChart', categoryTrend.map(d=>d.name), categoryTrend.map(d=>d.count), '#ffc000', 'Tickets');
        }
    }
}

// ── Exports ────────────────────────────────
function exportPDF(tableId, titleText) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    doc.setFontSize(14);
    doc.text(titleText, 14, 15);
    doc.setFontSize(10);
    doc.text('Generated: ' + new Date().toLocaleString(), 14, 22);

    doc.autoTable({
        html: '#' + tableId,
        startY: 28,
        theme: 'grid',
        styles: { font: 'helvetica', fontSize: 9 },
        headStyles: { fillColor: [30, 41, 59], textColor: 255 },
    });

    doc.save(tableId + '_' + Date.now() + '.pdf');
}

function exportExcel(tableId, sheetName) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table, {sheet: sheetName});
    XLSX.writeFile(wb, tableId + '_' + Date.now() + '.xlsx');
}

// ── Department Filtering by Company ──────────────
function filterDepts(prefix, companyId) {
    const deptSelect = document.getElementById(prefix + '-dept-select');
    if (!deptSelect) return;
    const currentDeptId = '{{ request("department_id") }}';
    const options = deptSelect.querySelectorAll('option');
    options.forEach(opt => {
        if (!opt.value) { opt.style.display = ''; return; } // "All Departments"
        if (!companyId) {
            opt.style.display = '';
        } else {
            opt.style.display = (opt.dataset.company == companyId) ? '' : 'none';
        }
    });
    // If currently selected dept doesn't belong to new company, reset
    const selectedOpt = deptSelect.querySelector('option:checked');
    if (selectedOpt && selectedOpt.value && companyId && selectedOpt.dataset.company != companyId) {
        deptSelect.value = '';
    }
}

// ── On Load: determine active tab from request ─
window.addEventListener('DOMContentLoaded', () => {
    const activeTab = '{{ request("active_tab", "company") }}';
    const validTabs = ['company','staff','period'];
    const tab = validTabs.includes(activeTab) ? activeTab : 'company';

    // Activate correct tab btn
    document.querySelectorAll('.dent-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    const btn = document.getElementById('tab-btn-' + tab);
    if (btn) btn.classList.add('active');
    const pane = document.getElementById(tab + '-tab');
    if (pane) pane.classList.add('active');

    drawChartsForTab(tab);

    // Apply company filter to dept dropdown on page load (for persisted filters)
    const companySelectCmp = document.getElementById('cmp-company-select');
    if (companySelectCmp && companySelectCmp.value) {
        filterDepts('cmp', companySelectCmp.value);
    }
    const companySelectStf = document.getElementById('stf-company-select');
    if (companySelectStf && companySelectStf.value) {
        filterDepts('stf', companySelectStf.value);
    }
});

// ── Sidebar hamburger (mobile only – handled by global style.css) ──
const menuToggle = document.getElementById('menuToggle');
if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        document.querySelector('.sidebar-nav')?.classList.toggle('open');
    });
}
</script>

</body>
</html>
