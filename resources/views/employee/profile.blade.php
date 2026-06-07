<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profile | Resolve</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/dist/css/style.css">
    <link rel="stylesheet" href="/dist/css/employ.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        @include('employee.partials.sidebar')

        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
                <div style="flex: 1;"></div>
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn active">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
            </header>

            <!-- Profile Content -->
            <div class="dashboard-content">
                <div class="breadcrumb">
                    <a href="{{ route('employee.dashboard') }}">Dashboard</a> &gt;
                    <span>Profile</span>
                </div>

                <div class="profile-card" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <div class="profile-header" style="text-align: center; margin-bottom: 2rem;">
                        <div class="avatar" style="width: 100px; height: 100px; background: #e0e7ff; color: #4338ca; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 1rem;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <h1 style="margin: 0; color: #111827;">{{ $user->name }}</h1>
                        <p style="color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.5rem;">EMPLOYEE</p>
                    </div>

                    <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                        <div class="info-item">
                            <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #6b7280; margin-bottom: 0.5rem;">EMPLOYEE ID</label>
                            <p style="margin: 0; font-size: 1rem; color: #111827; font-weight: 600;">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="info-item">
                            <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #6b7280; margin-bottom: 0.5rem;">EMAIL ADDRESS</label>
                            <p style="margin: 0; font-size: 1rem; color: #111827; font-weight: 600;">{{ $user->email }}</p>
                        </div>
                        <div class="info-item">
                            <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #6b7280; margin-bottom: 0.5rem;">PHONE NUMBER</label>
                            <p style="margin: 0; font-size: 1rem; color: #111827; font-weight: 600;">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="info-item">
                            <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #6b7280; margin-bottom: 0.5rem;">ORGANIZATION</label>
                            <p style="margin: 0; font-size: 1rem; color: #111827; font-weight: 600;">Resolve</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
