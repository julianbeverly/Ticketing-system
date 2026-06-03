<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\DentController;
use App\Http\Controllers\ChatController;

/****************** Authentication Routes ******************/

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('authentication.login');

Route::get('/otp', [AuthController::class, 'showOtp'])->name('authentication.otp');
Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('authentication.otp.verify');
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('authentication.otp.resend');

Route::get('/forget', [AuthController::class, 'showForget'])->name('password.request');
Route::post('/forget', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/resetpass', [AuthController::class, 'showResetPass'])->name('password.reset');
Route::post('/resetpass', [AuthController::class, 'resetPassword'])->name('password.update');


// logout route
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});



/****************** Protected Routes ******************/
Route::middleware(['auth'])->group(function () {
    // ==========================================
    // ADMIN ROUTES
    // ==========================================
    Route::middleware(['role:admin'])->group(function () {
        // Dashboards & Analytics
        Route::get('/dash', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/admin/chart-data', [DashboardController::class, 'getChartData'])->name('admin.chart.data');
        Route::get('/admin/sla-data', [DashboardController::class, 'getSlaData'])->name('admin.sla.data');
        
        // Tickets
        Route::get('/ticket', [TicketController::class, 'index'])->name('admin.tickets');
        Route::get('/createticket', [TicketController::class, 'create'])->name('admin.tickets.create');
        Route::get('/ticketdetails/{ticket}', [TicketController::class, 'details'])->name('admin.tickets.details');
        Route::get('/assigntech/{ticket}', [TicketController::class, 'assignTech'])->name('admin.assign');
        Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assignTechnician'])->name('admin.tickets.assign');
        Route::get('/dent', [TicketController::class, 'dent'])->name('admin.incidents');
        
        // Settings & SLA
        Route::get('/slaconfig', [TicketController::class, 'slaConfig'])->name('admin.slaconfig');
        Route::post('/slaconfig', [TicketController::class, 'updateSlaConfig'])->name('admin.slaconfig.update');
        
        // Reports
        Route::get('/techreport', [TicketController::class, 'report'])->name('tech.reporting');
        Route::get('/techreport/{user}', [TicketController::class, 'techReportDetails'])->name('admin.techreport.details');
        Route::get('/ticketreport', [TicketController::class, 'ticketReport'])->name('admin.ticketreport');
        Route::get('/ticketreport/export/excel', [TicketController::class, 'exportExcel'])->name('admin.ticketreport.export.excel');
        Route::get('/ticketreport/export/pdf', [TicketController::class, 'exportPdf'])->name('admin.ticketreport.export.pdf');
        Route::get('/techreport/export/excel', [TicketController::class, 'exportTechExcel'])->name('admin.techreport.export.excel');
        Route::get('/techreport/export/pdf', [TicketController::class, 'exportTechPdf'])->name('admin.techreport.export.pdf');
        
        // Users Management
        Route::get('/users', [UserController::class, 'index'])->name('user.index');
        Route::post('/users/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('user.update');
        Route::post('/users/{user}/suspend', [UserController::class, 'toggleSuspend'])->name('user.suspend');
        
        // Incident Settings (Categories & Types)
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/types', [TypeController::class, 'store'])->name('types.store');
        Route::put('/types/{type}', [TypeController::class, 'update'])->name('types.update');
        Route::delete('/types/{type}', [TypeController::class, 'destroy'])->name('types.destroy');
    });

    // ==========================================
    // TECHNICIAN ROUTES
    // ==========================================
    Route::middleware(['role:technician'])->group(function () {
        Route::get('/techdash', [DashboardController::class, 'technician'])->name('tech.dashboard');
        
        Route::get('/techticket', [TicketController::class, 'techTickets'])->name('tech.tickets');
        Route::get('/techticketdetails/{ticket}', [TicketController::class, 'techDetails'])->name('tech.tickets.details');
        Route::post('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tech.tickets.status');
        Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve'])->name('tech.tickets.resolve');
        
        Route::get('/myreport', [TicketController::class, 'techOwnReport'])->name('tech.own.report');
        Route::get('/mydetails', [TicketController::class, 'techOwnReportDetails'])->name('tech.own.report.details');
        Route::get('/myreport/export/excel', [TicketController::class, 'exportTechOwnExcel'])->name('tech.own.report.export.excel');
        Route::get('/myreport/export/pdf', [TicketController::class, 'exportTechOwnPdf'])->name('tech.own.report.export.pdf');
    });

    // ==========================================
    // EMPLOYEE ROUTES
    // ==========================================
    Route::middleware(['role:employee'])->group(function () {
        Route::get('/employeedash', [DashboardController::class, 'employee'])->name('employee.dashboard');
        
        Route::get('/employticket', [TicketController::class, 'employeeTickets'])->name('employee.tickets');
        Route::get('/employticketdetails/{ticket}', [TicketController::class, 'employeeDetails'])->name('employee.tickets.details');
        Route::get('/employee/createticket', [TicketController::class, 'employeeCreateTicket'])->name('employee.tickets.create');
        Route::post('/employee/tickets', [TicketController::class, 'store'])->name('employee.tickets.store');
        Route::post('/tickets/{ticket}/respond', [TicketController::class, 'respondToResolution'])->name('employee.tickets.respond');
        
        Route::get('/employee/ticketreport', [TicketController::class, 'employeeTicketReport'])->name('employee.ticketreport');
        Route::get('/employee/ticketreport/export/excel', [TicketController::class, 'exportEmployeeExcel'])->name('employee.ticketreport.export.excel');
        Route::get('/employee/ticketreport/export/pdf', [TicketController::class, 'exportEmployeePdf'])->name('employee.ticketreport.export.pdf');
    });

    // ==========================================
    // SHARED ROUTES (Available to all logged-in users)
    // ==========================================
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('employee.profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('employee.profile.update');
    
    // Chat
    Route::get('/tickets/{ticket}/messages', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
    Route::post('/tickets/{ticket}/messages', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/online-count', [ChatController::class, 'getOnlineCount'])->name('chat.online');
});

// use App\Models\User;

// Route::get('/create-admin', function () {
//     User::create([
//         'name' => 'Admin',
//         'email' => 'admin@gmail.com',
//         'password' => bcrypt('admin123'),
//         'role' => 'admin',
//         'speciality' => 'IT',
//         'status' => 'active',
//         'phone' => '670000000'
//     ]);

//     return "Admin created";
// });