<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// ===== Guest (login) =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ===== Admin =====
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Sections (admin creates these first — grade level + capacity)
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
        Route::patch('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

        // Register Page for Teacher (Admin Only can Create)
        Route::get('/teachers/register', [TeacherController::class, 'register'])->name('teachers.register');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::patch('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');

        // Disable / re-enable a teacher's login account
        Route::patch('/teachers/{teacher}/disable', [TeacherController::class, 'disable'])->name('teachers.disable');
        Route::patch('/teachers/{teacher}/enable', [TeacherController::class, 'enable'])->name('teachers.enable');

        // Assign Teacher (edit grade level / section / subject, per teacher)
        Route::get('/teachers/assign', [TeacherController::class, 'assign'])->name('teachers.assign');
        Route::patch('/teachers/{teacher}/assign', [TeacherController::class, 'assignUpdate'])->name('teachers.assign.update');

        // Add / Edit Student
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::patch('/students/{student}', [StudentController::class, 'update'])->name('students.update');

        // Attendance — dashboard, per-student history, manual override, CSV export
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/students/{student}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::get('/attendance/students/{student}/export', [AttendanceController::class, 'exportCsv'])->name('attendance.export');
        Route::post('/attendance/students/{student}/mark', [AttendanceController::class, 'markManual'])->name('attendance.mark');

        // Announcement — posting now also sends the SMTP email in one step,
        // so there is no separate "Email Announcement" page/route anymore.
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::patch('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    });

// ===== Teacher =====
Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    });
