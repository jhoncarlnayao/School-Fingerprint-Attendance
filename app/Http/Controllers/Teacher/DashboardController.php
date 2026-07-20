<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AttendanceLog;
use App\Models\Student;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        $sectionStudentCount = 0;
        $presentToday = 0;
        $absentToday = 0;
        $notScannedToday = 0;
        $attendanceRate = 0;
        $chartLabels = [];
        $chartPresent = [];
        $chartAbsent = [];
        $recentLogs = collect();
        $weekStrip = [];

        if ($teacher->assigned_grade_level && $teacher->assigned_section) {
            $studentIds = Student::where('grade_level', $teacher->assigned_grade_level)
                ->where('section', $teacher->assigned_section)
                ->pluck('id');

            $sectionStudentCount = $studentIds->count();
            $today = Carbon::today();

            $todayLogs = AttendanceLog::with('student')
                ->whereIn('student_id', $studentIds)
                ->forDate($today)
                ->get();

            $presentToday = $todayLogs->whereIn('status', [AttendanceLog::STATUS_PRESENT, AttendanceLog::STATUS_LATE])->count();
            $absentToday = $todayLogs->where('status', AttendanceLog::STATUS_ABSENT)->count();
            $notScannedToday = max($sectionStudentCount - $todayLogs->count(), 0);

            $recentLogs = $todayLogs->sortByDesc('time_in')->take(4)->values();

            // Last 7 days, for the bar chart.
            for ($i = 6; $i >= 0; $i--) {
                $d = $today->copy()->subDays($i);
                $dPresent = AttendanceLog::whereIn('student_id', $studentIds)
                    ->forDate($d)
                    ->whereIn('status', [AttendanceLog::STATUS_PRESENT, AttendanceLog::STATUS_LATE])
                    ->count();

                $chartLabels[] = $d->format('D');
                $chartPresent[] = $dPresent;
                $chartAbsent[] = max($sectionStudentCount - $dPresent, 0);
            }

            $possible = $sectionStudentCount * 7;
            $attendanceRate = $possible > 0 ? round(array_sum($chartPresent) / $possible * 100) : 0;

            // Current week strip (Mon–Sun) for the calendar card.
            $startOfWeek = $today->copy()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $d = $startOfWeek->copy()->addDays($i);
                $dPresent = $d->isFuture() ? null : AttendanceLog::whereIn('student_id', $studentIds)
                    ->forDate($d)
                    ->whereIn('status', [AttendanceLog::STATUS_PRESENT, AttendanceLog::STATUS_LATE])
                    ->count();

                $weekStrip[] = [
                    'label' => $d->format('D'),
                    'day' => $d->format('d'),
                    'isToday' => $d->isToday(),
                    'rate' => ($dPresent !== null && $sectionStudentCount > 0) ? round($dPresent / $sectionStudentCount * 100) : null,
                ];
            }
        }

        // Announcements meant for this teacher: "all" audience or "teachers" audience.
        $announcements = Announcement::whereIn('audience', ['all', 'teachers'])
            ->latest()
            ->take(6)
            ->get();

        return view('teacher.dashboard', compact(
            'teacher', 'sectionStudentCount', 'announcements',
            'presentToday', 'absentToday', 'notScannedToday', 'attendanceRate',
            'chartLabels', 'chartPresent', 'chartAbsent', 'recentLogs', 'weekStrip'
        ));
    }
}