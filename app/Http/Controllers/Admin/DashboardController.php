<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\AttendanceLog;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $unassignedTeachers = User::where('role', 'teacher')->whereNull('assigned_section')->count();
        $totalSections = Section::count();
        $totalAnnouncements = Announcement::count();

        $todaysLogs = AttendanceLog::forDate(Carbon::today())->get();
        $presentToday = $todaysLogs->whereIn('status', [AttendanceLog::STATUS_PRESENT, AttendanceLog::STATUS_LATE])->count();
        $absentToday = max($totalStudents - $presentToday, 0);
        $attendanceRate = $totalStudents > 0 ? round(($presentToday / $totalStudents) * 100) : 0;

        // Present/absent for the last 5 school days, for the dashboard chart.
        $chartLabels = [];
        $chartPresent = [];
        $chartAbsent = [];

        $day = Carbon::today();
        $collected = 0;
        while ($collected < 5) {
            if (! $day->isWeekend()) {
                $dayLogs = AttendanceLog::forDate($day)->get();
                $dayPresent = $dayLogs->whereIn('status', [AttendanceLog::STATUS_PRESENT, AttendanceLog::STATUS_LATE])->count();

                array_unshift($chartLabels, $day->format('D'));
                array_unshift($chartPresent, $dayPresent);
                array_unshift($chartAbsent, max($totalStudents - $dayPresent, 0));
                $collected++;
            }
            $day = $day->copy()->subDay();
        }

        // Real admin audit trail — what admins actually did, not a guess.
        // Same source powers the notification bell in the layout.
        $recentActivity = ActivityLog::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'unassignedTeachers',
            'totalSections',
            'totalAnnouncements',
            'presentToday',
            'absentToday',
            'attendanceRate',
            'chartLabels',
            'chartPresent',
            'chartAbsent',
            'recentActivity'
        ));
    }
}
